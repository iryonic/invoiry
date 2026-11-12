<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show profile management overview, active sessions, and security history.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $currentSessionId = $request->session()->getId();

        // 1. Retrieve Active Database Sessions
        $activeSessions = collect();
        if (config('session.driver') === 'database') {
            $rawSessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderByDesc('last_activity')
                ->get();

            $activeSessions = $rawSessions->map(function ($s) use ($currentSessionId) {
                return (object) [
                    'id' => $s->id,
                    'ip_address' => $s->ip_address,
                    'is_current' => $s->id === $currentSessionId,
                    'last_active' => Carbon::createFromTimestamp($s->last_activity)->diffForHumans(),
                    'last_active_at' => Carbon::createFromTimestamp($s->last_activity)->format('d M Y, h:i A'),
                    'device' => $this->parseAgent($s->user_agent),
                ];
            });
        }

        // 2. Retrieve Recent User Security Activity Logs
        $securityLogs = AuditLog::where('user_id', $user->id)
            ->latest('id')
            ->take(10)
            ->get();

        return view('profile.edit', compact('user', 'activeSessions', 'securityLogs'));
    }

    /**
     * Update basic personal profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $emailChanged = $user->email !== $validated['email'];

        $user->fill($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        AuditLog::record('user.profile_updated', "User {$user->name} updated personal contact details", $user->tenant_id, $user->id);

        return back()->with('success', 'Profile details updated successfully.');
    }

    /**
     * Upload or replace profile avatar photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete existing avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        AuditLog::record('user.avatar_updated', "User {$user->name} updated profile photo", $user->tenant_id, $user->id);

        return back()->with('success', 'Profile photo updated successfully.');
    }

    /**
     * Remove existing profile avatar photo.
     */
    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        AuditLog::record('user.avatar_removed', "User {$user->name} removed custom profile photo", $user->tenant_id, $user->id);

        return back()->with('success', 'Profile photo removed.');
    }

    /**
     * Update account password with current verification & strength rules.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers(), 'confirmed'],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::record('user.password_changed', "User {$user->name} changed their account password", $user->tenant_id, $user->id);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Terminate / Revoke a specific active database session.
     */
    public function destroySession(Request $request, string $sessionId): RedirectResponse
    {
        $user = $request->user();

        if ($sessionId === $request->session()->getId()) {
            return back()->with('warning', 'To terminate your current active session, please use Sign Out.');
        }

        DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->delete();

        AuditLog::record('user.session_revoked', "User {$user->name} terminated session {$sessionId}", $user->tenant_id, $user->id);

        return back()->with('success', 'Session terminated successfully.');
    }

    /**
     * Log out all other device sessions.
     */
    public function logoutOtherSessions(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $currentId = $request->session()->getId();

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentId)
            ->delete();

        AuditLog::record('user.logout_other_devices', "User {$user->name} terminated all other active sessions", $user->tenant_id, $user->id);

        return back()->with('success', 'All other active device sessions have been terminated.');
    }

    /**
     * Resend email verification notification or mark verified.
     */
    public function resendVerification(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return back()->with('info', 'Your email is already verified.');
        }

        $user->update(['email_verified_at' => now()]);

        AuditLog::record('user.email_verified', "User {$user->name} verified their email address ({$user->email})", $user->tenant_id, $user->id);

        return back()->with('success', 'Email verification confirmed successfully.');
    }

    /**
     * Cleanly parse human-readable device & browser from User Agent string.
     */
    protected function parseAgent(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Unknown Device / Browser';
        }

        $platform = 'Unknown OS';
        if (str_contains($userAgent, 'Windows')) {
            $platform = 'Windows PC';
        } elseif (str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Mac OS')) {
            $platform = 'Mac';
        } elseif (str_contains($userAgent, 'iPhone')) {
            $platform = 'iPhone';
        } elseif (str_contains($userAgent, 'iPad')) {
            $platform = 'iPad';
        } elseif (str_contains($userAgent, 'Android')) {
            $platform = 'Android Device';
        } elseif (str_contains($userAgent, 'Linux')) {
            $platform = 'Linux';
        }

        $browser = 'Browser';
        if (str_contains($userAgent, 'Edg/')) {
            $browser = 'Edge';
        } elseif (str_contains($userAgent, 'Chrome/')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgent, 'Safari/')) {
            $browser = 'Safari';
        } elseif (str_contains($userAgent, 'Firefox/')) {
            $browser = 'Firefox';
        }

        return "{$browser} on {$platform}";
    }
}
