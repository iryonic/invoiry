<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\DynamicFormConfig;
use App\Services\RbacPermissionManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    /**
     * Platform Overview / Dashboard.
     */
    public function index(): View
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $totalInvoices = Invoice::count();
        $totalGmv = (float) Invoice::sum('grand_total');
        $totalItemsTracked = Item::count();
        $totalUsers = User::count();

        $planStats = [
            'trial' => Tenant::where('plan', 'trial')->count(),
            'starter' => Tenant::where('plan', 'starter')->count(),
            'pro' => Tenant::where('plan', 'pro')->count(),
            'enterprise' => Tenant::where('plan', 'enterprise')->count(),
        ];

        $recentTenants = Tenant::withCount(['users', 'invoices', 'items'])
            ->latest()
            ->take(6)
            ->get();

        $recentAuditLogs = AuditLog::with('user')
            ->latest('id')
            ->take(8)
            ->get();

        return view('superadmin.index', compact(
            'totalTenants',
            'activeTenants',
            'totalInvoices',
            'totalGmv',
            'totalItemsTracked',
            'totalUsers',
            'planStats',
            'recentTenants',
            'recentAuditLogs'
        ));
    }

    /**
     * Dedicated Tenants Directory with Search & Filtering.
     */
    public function tenantsIndex(Request $request): View
    {
        $query = Tenant::withCount(['users', 'invoices', 'items']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('gstin', 'like', "%{$search}%");
            });
        }

        if ($plan = $request->input('plan')) {
            if ($plan !== 'all') {
                $query->where('plan', $plan);
            }
        }

        if ($type = $request->input('business_type')) {
            if ($type !== 'all') {
                $query->where('business_type', $type);
            }
        }

        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $tenants = $query->latest()->paginate(15)->withQueryString();

        return view('superadmin.tenants.index', compact('tenants'));
    }

    /**
     * Show form to create a new tenant.
     */
    public function createTenant(): View
    {
        return view('superadmin.create-tenant');
    }

    /**
     * Store new tenant & initial admin account.
     */
    public function storeTenant(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:tenants,email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'business_type' => ['required', 'in:selling,repair,both'],
            'plan' => ['required', 'in:trial,starter,pro,enterprise'],
            'admin_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'gstin' => ['nullable', 'string', 'max:30'],
            'upi_id' => ['nullable', 'string', 'max:100'],
        ], [
            'name.required' => 'Please enter the Business / Store Name.',
            'email.required' => 'Please enter the Store Official Email address.',
            'email.email' => 'Please provide a valid email address (e.g. store@domain.com).',
            'email.unique' => 'This email address is already registered to another store or admin user.',
            'phone.required' => 'Please enter the Store Phone Number.',
            'business_type.required' => 'Please choose a Business Type (Selling, Repair, or Both).',
            'business_type.in' => 'Invalid business type chosen.',
            'plan.required' => 'Please choose a subscription plan.',
            'admin_name.required' => 'Please enter the Store Administrator Full Name.',
            'password.required' => 'Please enter an admin login password.',
            'password.min' => 'The admin password must be at least 6 characters long.',
        ]);

        $validated['email'] = strtolower(trim($validated['email']));

        $permissions = $request->has('permissions')
            ? RbacPermissionManager::normalizeFromRequest($request->input('permissions', []))
            : RbacPermissionManager::getDefaultPermissions($validated['business_type']);

        $formFields = $request->has('form_fields')
            ? DynamicFormConfig::normalizeFromRequest($request->input('form_fields', []))
            : DynamicFormConfig::getDefaultConfig();

        $baseSlug = Str::slug($validated['name']);
        if (empty($baseSlug)) {
            $baseSlug = 'store-'.Str::lower(Str::random(6));
        }
        $slug = $baseSlug;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.Str::lower(Str::random(4));
        }

        $defaultTrialDays = (int) SystemSetting::get('default_trial_days', 30);

        try {
            [$tenant, $admin] = DB::transaction(function () use ($validated, $slug, $defaultTrialDays, $permissions, $formFields, $request) {
                $tenant = Tenant::create([
                    'name' => $validated['name'],
                    'slug' => $slug,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'business_type' => $validated['business_type'],
                    'plan' => $validated['plan'],
                    'address' => $validated['address'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'state' => $validated['state'] ?? 'Jammu & Kashmir',
                    'gstin' => $validated['gstin'] ?? null,
                    'upi_id' => $validated['upi_id'] ?? null,
                    'repair_job_prefix' => $request->input('repair_job_prefix', 'REP-') ?: 'REP-',
                    'repair_job_counter' => 1,
                    'expires_at' => now()->addDays($defaultTrialDays),
                    'is_active' => true,
                    'permissions' => $permissions,
                    'form_fields' => $formFields,
                ]);

                $admin = User::create([
                    'tenant_id' => $tenant->id,
                    'name' => $validated['admin_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'tenant_admin',
                    'is_active' => true,
                ]);

                return [$tenant, $admin];
            });

            try {
                AuditLog::record('tenant.created', "Created new store \"{$tenant->name}\" with customized RBAC & form permissions", $tenant->id);
            } catch (\Throwable $auditEx) {
                Log::warning('Failed to record tenant.created audit log: '.$auditEx->getMessage());
            }

            return redirect()->route('superadmin.tenants.index')->with('success', "Business \"{$tenant->name}\" registered successfully with admin \"{$admin->email}\"!");
        } catch (\Throwable $e) {
            Log::error('SuperAdmin storeTenant failed: '.$e->getMessage(), [
                'exception' => $e,
                'input' => $request->except(['password']),
            ]);

            $message = $e->getMessage();
            if (str_contains($message, 'UNIQUE constraint failed: users.email') || str_contains($message, 'users_email_unique')) {
                $friendlyError = "The email address '{$validated['email']}' is already assigned to an existing user account. Please use a unique email address.";
            } elseif (str_contains($message, 'UNIQUE constraint failed: tenants.slug') || str_contains($message, 'tenants_slug_unique')) {
                $friendlyError = 'A store with a similar name / web URL already exists. Please adjust the store name slightly.';
            } elseif (str_contains($message, 'UNIQUE constraint failed: tenants.email')) {
                $friendlyError = "The store email '{$validated['email']}' is already registered to another store.";
            } else {
                $friendlyError = 'Unable to provision new business store: '.$message;
            }

            return back()->withInput()->with('error', $friendlyError);
        }
    }

    /**
     * Show Tenant Details & Metrics.
     */
    public function showTenant(Tenant $tenant): View
    {
        $tenant->loadCount(['users', 'invoices', 'items', 'spareParts', 'repairJobs']);
        $tenantUsers = User::where('tenant_id', $tenant->id)->latest()->get();
        $recentInvoices = Invoice::where('tenant_id', $tenant->id)->with('customer')->latest()->take(10)->get();
        $recentStock = Item::where('tenant_id', $tenant->id)->latest()->take(10)->get();
        $recentRepairJobs = $tenant->repairJobs()->with(['customer', 'technician'])->latest()->take(10)->get();

        $totalSales = (float) Invoice::where('tenant_id', $tenant->id)->sum('grand_total');
        $totalCollected = (float) Invoice::where('tenant_id', $tenant->id)->sum('amount_paid');
        $inStockDevices = Item::where('tenant_id', $tenant->id)->inStock()->count();
        $totalRepairRevenue = (float) $tenant->repairJobs()->sum('total_amount');

        return view('superadmin.tenants.show', compact(
            'tenant',
            'tenantUsers',
            'recentInvoices',
            'recentStock',
            'recentRepairJobs',
            'totalSales',
            'totalCollected',
            'inStockDevices',
            'totalRepairRevenue'
        ));
    }

    /**
     * Edit Tenant Details.
     */
    public function editTenant(Tenant $tenant): View
    {
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    /**
     * Update Tenant Details.
     */
    public function updateTenant(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('tenants')->ignore($tenant->id)],
            'phone' => ['required', 'string', 'max:20'],
            'business_type' => ['required', 'in:selling,repair,both'],
            'plan' => ['required', 'in:trial,starter,pro,enterprise'],
            'expires_at' => ['nullable', 'date'],
            'invoice_prefix' => ['nullable', 'string', 'max:10'],
            'repair_job_prefix' => ['nullable', 'string', 'max:10'],
            'gstin' => ['nullable', 'string', 'max:30'],
            'upi_id' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $tenant->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'business_type' => $validated['business_type'],
            'plan' => $validated['plan'],
            'expires_at' => $validated['expires_at'] ?? $tenant->expires_at,
            'invoice_prefix' => $validated['invoice_prefix'] ?? $tenant->invoice_prefix,
            'repair_job_prefix' => $validated['repair_job_prefix'] ?? ($tenant->repair_job_prefix ?: 'REP-'),
            'gstin' => $validated['gstin'] ?? null,
            'upi_id' => $validated['upi_id'] ?? null,
            'address' => $validated['address'] ?? $tenant->address,
            'city' => $validated['city'] ?? $tenant->city,
            'state' => $validated['state'] ?? $tenant->state ?? 'Jammu & Kashmir',
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $tenant->is_active,
        ]);

        AuditLog::record('tenant.updated', "Updated store details for \"{$tenant->name}\"", $tenant->id);

        return redirect()->route('superadmin.tenants.show', $tenant)->with('success', "Store \"{$tenant->name}\" updated successfully.");
    }

    /**
     * Extend tenant subscription.
     */
    public function extendSubscription(Request $request, Tenant $tenant): RedirectResponse
    {
        $days = (int) $request->input('days', 30);
        $baseDate = ($tenant->expires_at && $tenant->expires_at->isFuture()) ? $tenant->expires_at : now();
        $newExpiry = $baseDate->copy()->addDays($days);

        $tenant->update(['expires_at' => $newExpiry, 'is_active' => true]);

        AuditLog::record('tenant.subscription_extended', "Extended subscription for {$tenant->name} by {$days} days", $tenant->id);

        return back()->with('success', "Subscription for \"{$tenant->name}\" extended by {$days} days (valid until {$newExpiry->format('d M Y')}).");
    }

    /**
     * Show dedicated RBAC & Form Configuration interface for an existing tenant.
     */
    public function permissionsIndex(Tenant $tenant): View
    {
        $permissions = $tenant->permissions ?? RbacPermissionManager::getDefaultPermissions();
        $formConfig = $tenant->form_fields ?? DynamicFormConfig::getDefaultConfig();

        return view('superadmin.tenants.permissions', compact('tenant', 'permissions', 'formConfig'));
    }

    /**
     * Update permissions & form configurations for an existing tenant.
     */
    public function permissionsUpdate(Request $request, Tenant $tenant): RedirectResponse
    {
        $permissions = RbacPermissionManager::normalizeFromRequest($request->input('permissions', []));
        $formFields = DynamicFormConfig::normalizeFromRequest($request->input('form_fields', []));

        $tenant->update([
            'permissions' => $permissions,
            'form_fields' => $formFields,
        ]);

        AuditLog::record('tenant.permissions_updated', "Updated RBAC permissions and form configuration for store \"{$tenant->name}\"", $tenant->id);

        return back()->with('success', "Permissions and form configurations for \"{$tenant->name}\" have been updated successfully.");
    }

    /**
     * Delete tenant store.
     */
    public function destroyTenant(Tenant $tenant): RedirectResponse
    {
        $name = $tenant->name;
        $tenant->delete();

        AuditLog::record('tenant.deleted', "Deleted store \"{$name}\"");

        return redirect()->route('superadmin.tenants.index')->with('success', "Store \"{$name}\" and related data deleted.");
    }

    /**
     * Toggle tenant active status.
     */
    public function toggleStatus(Tenant $tenant): RedirectResponse
    {
        $tenant->update(['is_active' => ! $tenant->is_active]);
        $status = $tenant->is_active ? 'activated' : 'deactivated';

        AuditLog::record('tenant.status_toggled', "Tenant \"{$tenant->name}\" was {$status}", $tenant->id);

        return back()->with('success', "Tenant \"{$tenant->name}\" has been {$status}.");
    }

    /**
     * Switch context / Impersonate tenant.
     */
    public function impersonate(Tenant $tenant): RedirectResponse
    {
        Session::put('active_tenant_id', $tenant->id);
        AuditLog::record('tenant.impersonate_start', "Super Admin switched into store \"{$tenant->name}\"", $tenant->id);

        return redirect()->route('dashboard')->with('success', "Switched into \"{$tenant->name}\" view.");
    }

    /**
     * Leave impersonation and return to Super Admin.
     */
    public function leaveImpersonate(): RedirectResponse
    {
        $activeId = Session::get('active_tenant_id');
        Session::forget('active_tenant_id');
        AuditLog::record('tenant.impersonate_end', 'Super Admin returned to global console', $activeId);

        return redirect()->route('superadmin.index')->with('success', 'Returned to Super Admin console.');
    }

    /**
     * Global User Accounts Directory across All Tenants.
     */
    public function usersIndex(Request $request): View
    {
        $query = User::with('tenant');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            if ($role !== 'all') {
                $query->where('role', $role);
            }
        }

        if ($tenantId = $request->input('tenant_id')) {
            if ($tenantId !== 'all') {
                $query->where('tenant_id', $tenantId);
            }
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $tenants = Tenant::orderBy('name')->get();

        return view('superadmin.users.index', compact('users', 'tenants'));
    }

    /**
     * Store new user globally.
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:super_admin,tenant_admin,cashier'],
            'tenant_id' => ['nullable', 'required_if:role,tenant_admin,cashier', 'exists:tenants,id'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'tenant_id' => $validated['role'] === 'super_admin' ? null : $validated['tenant_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        AuditLog::record('user.created_by_admin', "Created user {$user->name} ({$user->role})", $user->tenant_id, $user->id);

        return back()->with('success', "User \"{$user->name}\" created successfully.");
    }

    /**
     * Show form to edit user account details.
     */
    public function editUser(User $user): View
    {
        $tenants = Tenant::orderBy('name')->get();

        return view('superadmin.users.edit', compact('user', 'tenants'));
    }

    /**
     * Update global user.
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:super_admin,tenant_admin,cashier'],
            'tenant_id' => ['nullable', 'required_if:role,tenant_admin,cashier', 'exists:tenants,id'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->role = $validated['role'];
        $user->tenant_id = $validated['role'] === 'super_admin' ? null : $validated['tenant_id'];
        $user->is_active = $request->has('is_active');

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AuditLog::record('user.updated_by_admin', "Updated user {$user->name} ({$user->role})", $user->tenant_id);

        if ($request->input('redirect_to') === 'index' || str_contains(url()->previous(), '/users/'.$user->id.'/edit')) {
            return redirect()->route('superadmin.users.index')->with('success', "User \"{$user->name}\" updated successfully.");
        }

        return back()->with('success', "User \"{$user->name}\" updated successfully.");
    }

    /**
     * Show granular permission management view for a specific user.
     */
    public function userPermissionsIndex(User $user): View
    {
        $tenantPermissions = $user->tenant?->permissions ?? RbacPermissionManager::getDefaultPermissions();
        $hasCustomOverrides = is_array($user->permissions) && ! empty($user->permissions);
        $permissions = $hasCustomOverrides ? $user->permissions : $tenantPermissions;

        return view('superadmin.users.permissions', compact('user', 'permissions', 'hasCustomOverrides', 'tenantPermissions'));
    }

    /**
     * Update granular permissions for a specific user.
     */
    public function userPermissionsUpdate(Request $request, User $user): RedirectResponse
    {
        if ($request->has('reset_to_default')) {
            $user->update(['permissions' => null]);
            AuditLog::record('user.permissions_reset', "Reset permissions for user \"{$user->name}\" to store defaults", $user->tenant_id, $user->id);

            return back()->with('success', "Permissions for \"{$user->name}\" have been reset to inherit store defaults.");
        }

        $permissions = RbacPermissionManager::normalizeFromRequest($request->input('permissions', []));

        $user->update(['permissions' => $permissions]);

        AuditLog::record('user.permissions_updated', "Updated custom permissions for user \"{$user->name}\"", $user->tenant_id, $user->id);

        return back()->with('success', "Custom permissions for \"{$user->name}\" updated successfully.");
    }

    /**
     * Delete a global user.
     */
    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own active administrator account.');
        }

        $name = $user->name;
        $user->delete();

        AuditLog::record('user.deleted_by_admin', "Deleted user account {$name}");

        return back()->with('success', "User \"{$name}\" deleted.");
    }

    /**
     * Toggle user active status.
     */
    public function toggleUserStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';

        AuditLog::record('user.status_toggled', "User {$user->name} {$status}", $user->tenant_id);

        return back()->with('success', "User account {$status}.");
    }

    /**
     * Global Audit Logs Viewer.
     */
    public function auditLogsIndex(Request $request): View
    {
        $query = AuditLog::with(['user', 'tenant']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('event', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($event = $request->input('event')) {
            if ($event !== 'all') {
                $query->where('event', 'like', "%{$event}%");
            }
        }

        $auditLogs = $query->latest('id')->paginate(20)->withQueryString();

        return view('superadmin.audit-logs.index', compact('auditLogs'));
    }

    /**
     * Platform System Settings Management.
     */
    public function settingsIndex(): View
    {
        $settings = [
            'app_version' => SystemSetting::get('app_version', 'v1.0.0'),
            'show_login_demo_credentials' => SystemSetting::isEnabled('show_login_demo_credentials', true),
            'platform_name' => SystemSetting::get('platform_name', 'INVOIRY'),
            'support_email' => SystemSetting::get('support_email', 'support@invoicepos.com'),
            'support_phone' => SystemSetting::get('support_phone', '+91 7006079473'),
            'default_trial_days' => SystemSetting::get('default_trial_days', '30'),
            'maintenance_mode' => SystemSetting::isEnabled('maintenance_mode', false),
            'maintenance_heading' => SystemSetting::get('maintenance_heading', 'Scheduled System Maintenance'),
            'maintenance_message' => SystemSetting::get('maintenance_message', 'The terminal database and POS infrastructure are undergoing scheduled optimization and compliance updates. Normal counter service will resume momentarily.'),
            'maintenance_reopen_at' => SystemSetting::get('maintenance_reopen_at', ''),
            'preloader_enabled' => SystemSetting::isEnabled('preloader_enabled', true),
            'preloader_min_time' => (int) SystemSetting::get('preloader_min_time', 0),
            'preloader_max_time' => (int) SystemSetting::get('preloader_max_time', 3500),
            'preloader_fade_duration' => (int) SystemSetting::get('preloader_fade_duration', 300),
            'announcement_banner' => SystemSetting::get('announcement_banner', ''),
            'footer_enabled' => SystemSetting::isEnabled('footer_enabled', true),
            'footer_copyright' => SystemSetting::get('footer_copyright', '© {year} Invoiry Terminal. All rights reserved.'),
            'footer_tagline' => SystemSetting::get('footer_tagline', 'Point-of-Sale & Multi-Store Retail Intelligence. Hardware Barcode & Thermal ESC/POS Ready.'),
            'footer_contact_info' => SystemSetting::get('footer_contact_info', 'Central Support: support@invoicepos.com · +91 7006079473'),
            'footer_badge_text' => SystemSetting::get('footer_badge_text', 'v2.4 Live POS'),
            'footer_link_1_text' => SystemSetting::get('footer_link_1_text', 'Privacy Policy'),
            'footer_link_1_url' => SystemSetting::get('footer_link_1_url', '#'),
            'footer_link_2_text' => SystemSetting::get('footer_link_2_text', 'Terms of Service'),
            'footer_link_2_url' => SystemSetting::get('footer_link_2_url', '#'),
            'footer_link_3_text' => SystemSetting::get('footer_link_3_text', 'Help & Documentation'),
            'footer_link_3_url' => SystemSetting::get('footer_link_3_url', '#'),
        ];

        return view('superadmin.settings.index', compact('settings'));
    }

    /**
     * Update Platform System Settings.
     */
    public function settingsUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_version' => ['nullable', 'string', 'max:20', 'regex:/^v?[0-9]+(\.[0-9]+)*(-[a-zA-Z0-9.]+)?$/'],
            'platform_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email', 'max:255'],
            'support_phone' => ['nullable', 'string', 'max:30'],
            'default_trial_days' => ['required', 'integer', 'min:1', 'max:365'],
            'announcement_banner' => ['nullable', 'string', 'max:500'],
            'maintenance_heading' => ['nullable', 'string', 'max:255'],
            'maintenance_message' => ['nullable', 'string', 'max:1000'],
            'maintenance_reopen_at' => ['nullable', 'string', 'max:100'],
            'preloader_min_time' => ['nullable', 'integer', 'min:0', 'max:5000'],
            'preloader_max_time' => ['nullable', 'integer', 'min:500', 'max:10000'],
            'preloader_fade_duration' => ['nullable', 'integer', 'min:50', 'max:2000'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
            'footer_tagline' => ['nullable', 'string', 'max:500'],
            'footer_contact_info' => ['nullable', 'string', 'max:255'],
            'footer_badge_text' => ['nullable', 'string', 'max:50'],
            'footer_link_1_text' => ['nullable', 'string', 'max:50'],
            'footer_link_1_url' => ['nullable', 'string', 'max:255'],
            'footer_link_2_text' => ['nullable', 'string', 'max:50'],
            'footer_link_2_url' => ['nullable', 'string', 'max:255'],
            'footer_link_3_text' => ['nullable', 'string', 'max:50'],
            'footer_link_3_url' => ['nullable', 'string', 'max:255'],
        ]);

        // Toggle checkbox values
        SystemSetting::set('show_login_demo_credentials', $request->has('show_login_demo_credentials') ? '1' : '0', 'login');
        SystemSetting::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0', 'system');
        SystemSetting::set('preloader_enabled', $request->has('preloader_enabled') ? '1' : '0', 'system');

        // Central Version
        if (! empty($validated['app_version'])) {
            SystemSetting::set('app_version', $validated['app_version'], 'general');
        }

        // Maintenance Details
        if (isset($validated['maintenance_heading'])) {
            SystemSetting::set('maintenance_heading', $validated['maintenance_heading'], 'system');
        }
        if (isset($validated['maintenance_message'])) {
            SystemSetting::set('maintenance_message', $validated['maintenance_message'], 'system');
        }
        if (isset($validated['maintenance_reopen_at'])) {
            SystemSetting::set('maintenance_reopen_at', $validated['maintenance_reopen_at'], 'system');
        }

        // Preloader Timing
        if (isset($validated['preloader_min_time'])) {
            SystemSetting::set('preloader_min_time', (string) $validated['preloader_min_time'], 'system');
        }
        if (isset($validated['preloader_max_time'])) {
            SystemSetting::set('preloader_max_time', (string) $validated['preloader_max_time'], 'system');
        }
        if (isset($validated['preloader_fade_duration'])) {
            SystemSetting::set('preloader_fade_duration', (string) $validated['preloader_fade_duration'], 'system');
        }

        // Text values
        SystemSetting::set('platform_name', $validated['platform_name'], 'general');
        SystemSetting::set('support_email', $validated['support_email'], 'general');
        SystemSetting::set('support_phone', $validated['support_phone'] ?? '', 'general');
        SystemSetting::set('default_trial_days', (string) $validated['default_trial_days'], 'subscription');
        SystemSetting::set('announcement_banner', $validated['announcement_banner'] ?? '', 'system');

        // Footer settings
        if ($request->has('footer_settings_submitted') || $request->has('footer_copyright') || $request->has('footer_enabled')) {
            SystemSetting::set('footer_enabled', $request->has('footer_enabled') ? '1' : '0', 'footer');
            SystemSetting::set('footer_copyright', $validated['footer_copyright'] ?? '© {year} Invoiry Terminal. All rights reserved.', 'footer');
            SystemSetting::set('footer_tagline', $validated['footer_tagline'] ?? '', 'footer');
            SystemSetting::set('footer_contact_info', $validated['footer_contact_info'] ?? '', 'footer');
            SystemSetting::set('footer_badge_text', $validated['footer_badge_text'] ?? '', 'footer');
            SystemSetting::set('footer_link_1_text', $validated['footer_link_1_text'] ?? '', 'footer');
            SystemSetting::set('footer_link_1_url', $validated['footer_link_1_url'] ?? '', 'footer');
            SystemSetting::set('footer_link_2_text', $validated['footer_link_2_text'] ?? '', 'footer');
            SystemSetting::set('footer_link_2_url', $validated['footer_link_2_url'] ?? '', 'footer');
            SystemSetting::set('footer_link_3_text', $validated['footer_link_3_text'] ?? '', 'footer');
            SystemSetting::set('footer_link_3_url', $validated['footer_link_3_url'] ?? '', 'footer');
        }

        AuditLog::record('settings.platform_updated', 'Super Admin updated global platform settings');

        return back()->with('success', 'Platform settings updated successfully.');
    }

    /**
     * Bulk Action on Tenants (Super Admin).
     */
    public function bulkTenantsAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:tenants,id'],
            'action' => ['required', 'string', 'in:activate,deactivate,suspend,delete'],
        ]);

        $count = count($validated['ids']);
        if ($validated['action'] === 'activate') {
            Tenant::whereIn('id', $validated['ids'])->update(['is_active' => true]);
            AuditLog::record('tenant.bulk_activated', "Super Admin bulk-activated {$count} stores");
            $msg = "{$count} stores activated successfully.";
        } elseif ($validated['action'] === 'deactivate' || $validated['action'] === 'suspend') {
            Tenant::whereIn('id', $validated['ids'])->update(['is_active' => false]);
            AuditLog::record('tenant.bulk_deactivated', "Super Admin bulk-deactivated {$count} stores");
            $msg = "{$count} stores suspended successfully.";
        } else {
            Tenant::whereIn('id', $validated['ids'])->delete();
            AuditLog::record('tenant.bulk_deleted', "Super Admin bulk-deleted {$count} stores");
            $msg = "{$count} stores deleted successfully.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Bulk Action on Platform Users (Super Admin).
     */
    public function bulkUsersAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:users,id'],
            'action' => ['required', 'string', 'in:activate,deactivate,suspend,delete'],
        ]);

        // Never self-terminate in bulk
        $ids = array_values(array_filter($validated['ids'], fn ($id) => (int) $id !== (int) auth()->id()));
        $count = count($ids);

        if ($count === 0) {
            return back()->with('warning', 'No eligible target accounts selected (cannot self-modify current administrator).');
        }

        if ($validated['action'] === 'activate') {
            User::whereIn('id', $ids)->update(['is_active' => true]);
            AuditLog::record('user.bulk_activated', "Super Admin bulk-activated {$count} user accounts");
            $msg = "{$count} accounts activated successfully.";
        } elseif ($validated['action'] === 'deactivate' || $validated['action'] === 'suspend') {
            User::whereIn('id', $ids)->update(['is_active' => false]);
            AuditLog::record('user.bulk_deactivated', "Super Admin bulk-deactivated {$count} user accounts");
            $msg = "{$count} accounts suspended successfully.";
        } else {
            User::whereIn('id', $ids)->delete();
            AuditLog::record('user.bulk_deleted', "Super Admin bulk-deleted {$count} user accounts");
            $msg = "{$count} accounts deleted successfully.";
        }

        return back()->with('success', $msg);
    }
}
