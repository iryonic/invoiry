<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\DynamicFormConfig;
use App\Services\RbacPermissionManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Standard Indian GST state mapping for automated code resolution.
     */
    public static array $gstStates = [
        '01' => 'Jammu and Kashmir',
        '02' => 'Himachal Pradesh',
        '03' => 'Punjab',
        '04' => 'Chandigarh',
        '05' => 'Uttarakhand',
        '06' => 'Haryana',
        '07' => 'Delhi',
        '08' => 'Rajasthan',
        '09' => 'Uttar Pradesh',
        '10' => 'Bihar',
        '11' => 'Sikkim',
        '12' => 'Arunachal Pradesh',
        '13' => 'Nagaland',
        '14' => 'Manipur',
        '15' => 'Mizoram',
        '16' => 'Tripura',
        '17' => 'Meghalaya',
        '18' => 'Assam',
        '19' => 'West Bengal',
        '20' => 'Jharkhand',
        '21' => 'Odisha',
        '22' => 'Chhattisgarh',
        '23' => 'Madhya Pradesh',
        '24' => 'Gujarat',
        '26' => 'Dadra & Nagar Haveli and Daman & Diu',
        '27' => 'Maharashtra',
        '29' => 'Karnataka',
        '30' => 'Goa',
        '31' => 'Lakshadweep',
        '32' => 'Kerala',
        '33' => 'Tamil Nadu',
        '34' => 'Puducherry',
        '35' => 'Andaman and Nicobar Islands',
        '36' => 'Telangana',
        '37' => 'Andhra Pradesh',
        '38' => 'Ladakh',
    ];

    /**
     * Show the login page.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (session('terminal_locked') === true) {
                return redirect()->route('lock');
            }

            $user = Auth::user();
            if ($user->isSuperAdmin()) {
                return redirect()->route('superadmin.index');
            }

            return redirect()->route('dashboard');
        }

        $showDemoCredentials = SystemSetting::isEnabled('show_login_demo_credentials', true);
        $platformName = SystemSetting::get('platform_name', 'INVOIRY');

        return view('auth.login', compact('showDemoCredentials', 'platformName'));
    }

    /**
     * Handle authentication attempt with rate limiting & audit logs.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['login']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'login' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('login');
        }

        $loginInput = trim($credentials['login']);
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$fieldType => $loginInput, 'password' => $credentials['password']], $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            $request->session()->forget('terminal_locked');
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();

                return back()->withErrors(['login' => 'Your account has been deactivated. Please contact support.']);
            }

            AuditLog::record('user.login', "User {$user->name} ({$user->role}) signed in to terminal via {$fieldType}", $user->tenant_id, $user->id);

            if ($user->isSuperAdmin()) {
                return redirect()->intended(route('superadmin.index'));
            }

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    /**
     * Show new store registration / onboarding page.
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $states = self::$gstStates;

        return view('auth.register', compact('states'));
    }

    /**
     * Process new store registration & admin user provisioning.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'unique:tenants,email'],
            'phone' => ['required', 'string', 'max:20'],
            'state_code' => ['required', 'string', 'max:2'],
            'gstin' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $stateName = self::$gstStates[$validated['state_code']] ?? 'Jammu and Kashmir';
        $slug = Str::slug($validated['store_name']);
        if (Tenant::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::random(4);
        }

        $defaultTrialDays = (int) SystemSetting::get('default_trial_days', 14);

        [$tenant, $user] = DB::transaction(function () use ($validated, $slug, $stateName, $defaultTrialDays) {
            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $validated['store_name']), 0, 3));
            if (empty($prefix) || strlen($prefix) < 2) {
                $prefix = 'INV';
            }

            $tenant = Tenant::create([
                'name' => $validated['store_name'],
                'slug' => $slug,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'state' => $stateName,
                'state_code' => $validated['state_code'],
                'gstin' => $validated['gstin'] ?? null,
                'plan' => 'trial',
                'expires_at' => now()->addDays($defaultTrialDays),
                'is_active' => true,
                'invoice_prefix' => $prefix,
                'invoice_counter' => 1,
                'default_gst_rate' => 18.00,
                'is_tax_inclusive' => false,
                'permissions' => RbacPermissionManager::getDefaultPermissions(),
                'form_fields' => DynamicFormConfig::getDefaultConfig(),
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'tenant_admin',
                'is_active' => true,
            ]);

            return [$tenant, $user];
        });

        AuditLog::record('tenant.registered', "New store \"{$tenant->name}\" registered by {$user->name} on 14-day trial", $tenant->id, $user->id);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', "Welcome to INVOIRY! Your 14-day store trial for \"{$tenant->name}\" is now active.");
    }

    /**
     * Show Forgot Password request form.
     */
    public function showForgotPassword(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.forgot-password');
    }

    /**
     * Generate password reset token.
     */
    public function forgotPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $throttleKey = 'pwd_reset|'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors(['email' => "Too many reset requests. Please wait {$seconds} seconds."]);
        }

        RateLimiter::hit($throttleKey, 60);

        $user = User::where('email', $validated['email'])->first();
        if ($user) {
            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
            session()->flash('demo_reset_link', $resetUrl);

            AuditLog::record('user.password_reset_request', "Password reset requested for {$user->email}", $user->tenant_id, $user->id);
        }

        return back()->with('status', 'If your email is registered in our terminal network, password reset instructions have been generated.');
    }

    /**
     * Show Reset Password form.
     */
    public function showResetPassword(Request $request, string $token): View|RedirectResponse
    {
        $email = $request->query('email');

        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Process password reset submission.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (! $resetRecord || ! Hash::check($validated['token'], $resetRecord->token)) {
            return back()->withErrors(['email' => 'This password reset link is invalid or has already been used.']);
        }

        // Check 60-minute expiry
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            return back()->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
        }

        $user = User::where('email', $validated['email'])->first();
        if (! $user) {
            return back()->withErrors(['email' => 'User account not found.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        AuditLog::record('user.password_reset_success', "Password successfully reset for {$user->email}", $user->tenant_id, $user->id);

        return redirect()->route('login')->with('success', 'Your password has been reset successfully. Please sign in with your new credentials.');
    }

    /**
     * Lock POS Terminal.
     */
    public function lock(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        session(['terminal_locked' => true]);
        $user = Auth::user();
        $tenant = $user->tenant;

        return view('auth.lock', compact('user', 'tenant'));
    }

    /**
     * Unlock POS Terminal with password or PIN.
     */
    public function unlock(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $throttleKey = 'terminal_unlock|'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors(['password' => "Too many incorrect unlock attempts. Please wait {$seconds} seconds."]);
        }

        if (! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors(['password' => 'Incorrect password. Terminal remains locked.']);
        }

        RateLimiter::clear($throttleKey);
        session()->forget('terminal_locked');
        AuditLog::record('terminal.unlocked', "User {$user->name} unlocked terminal", $user->tenant_id, $user->id);

        if ($user->isSuperAdmin()) {
            return redirect()->intended(route('superadmin.index'))->with('success', 'Terminal unlocked successfully.');
        }

        return redirect()->intended(route('dashboard'))->with('success', 'Terminal unlocked successfully.');
    }

    /**
     * One-click demo login helper for evaluation.
     */
    public function demoLogin(string $type): RedirectResponse
    {
        $user = match ($type) {
            'freetrial', 'shop_admin' => User::where('email', 'freetrial@gmail.com')->first(),
            'cashier', 'store_cashier' => User::where('email', 'cashier@freetrial.com')->first(),
            'superadmin', 'super_admin' => User::where('email', 'superadmin@invoice.com')->first(),
            'apex', 'apex_store' => User::where('email', 'apex@gmail.com')->first(),
            default => null,
        };

        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();
            request()->session()->forget('terminal_locked');

            if ($redirect = request()->query('redirect')) {
                if (str_starts_with($redirect, '/')) {
                    return redirect($redirect);
                }
            }

            if ($user->isSuperAdmin()) {
                return redirect()->route('superadmin.index')->with('success', "Logged in as {$user->name} (Platform Super Admin)");
            }

            return redirect()->route('dashboard')->with('success', "Logged in as {$user->name} ({$user->tenant?->name})");
        }

        return redirect()->route('login')->with('error', 'Evaluation user not found.');
    }

    /**
     * Log out all other device sessions.
     */
    public function logoutOtherDevices(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        if (! Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        Auth::logoutOtherDevices($request->current_password);
        AuditLog::record('user.logout_other_devices', 'User terminated all other active sessions', Auth::user()->tenant_id, Auth::id());

        return back()->with('success', 'All other active device sessions have been terminated.');
    }

    /**
     * Log out the current user.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::record('user.logout', "User {$user->name} signed out of terminal", $user->tenant_id, $user->id);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
