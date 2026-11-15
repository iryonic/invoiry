<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\EmailSetting;
use App\Services\MailService;
use App\Services\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MailSettingController extends Controller
{
    /**
     * Display the email delivery & SMTP configuration page.
     */
    public function index(Request $request, MailService $mailService): View
    {
        $tenant = TenantContext::get();
        $isSuperAdmin = auth()->user()?->isSuperAdmin() && ! session()->has('active_tenant_id');
        $tenantId = $isSuperAdmin ? null : $tenant?->id;

        $settings = EmailSetting::forTenantOrGlobal($tenantId)->latest()->get();

        $activeSetting = $mailService->getActiveSetting($tenant);

        // Predefined preset configurations for popular SMTP providers
        $presets = [
            'custom' => [
                'name' => 'Custom SMTP Server',
                'host' => '',
                'port' => 587,
                'encryption' => 'tls',
                'notes' => 'Direct connection to your private or self-hosted SMTP server.',
            ],
            'gmail' => [
                'name' => 'Gmail SMTP',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
                'notes' => 'Requires a Google App Password (not your personal Google account password).',
            ],
            'google_workspace' => [
                'name' => 'Google Workspace SMTP Relay',
                'host' => 'smtp-relay.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
                'notes' => 'Enterprise Google Workspace SMTP relay with domain-wide authentication.',
            ],
            'hostinger' => [
                'name' => 'Hostinger Webmail SMTP',
                'host' => 'smtp.hostinger.com',
                'port' => 465,
                'encryption' => 'ssl',
                'notes' => 'Standard secure SMTP provided with Hostinger cPanel / Titan email accounts.',
            ],
            'outlook' => [
                'name' => 'Microsoft 365 / Outlook',
                'host' => 'smtp.office365.com',
                'port' => 587,
                'encryption' => 'tls',
                'notes' => 'Office 365 / Exchange Online SMTP client submission.',
            ],
        ];

        return view('mail.settings.index', compact('settings', 'activeSetting', 'presets', 'tenant', 'isSuperAdmin'));
    }

    /**
     * Store a newly created SMTP setting.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get();
        $isSuperAdmin = auth()->user()?->isSuperAdmin() && ! session()->has('active_tenant_id');

        $validated = $request->validate([
            'provider_name' => ['required', 'string', 'in:custom,gmail,google_workspace,hostinger,outlook'],
            'display_name' => ['required', 'string', 'max:100'],
            'mail_host' => ['required', 'string', 'max:255'],
            'mail_port' => ['required', 'integer', 'between:1,65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['required', 'in:tls,ssl,none'],
            'from_address' => ['required', 'email', 'max:255'],
            'from_name' => ['required', 'string', 'max:100'],
            'reply_to_address' => ['nullable', 'email', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $tenantId = $isSuperAdmin ? null : $tenant?->id;

        // If setting as default, clear default flag from siblings
        if ($request->boolean('is_default')) {
            EmailSetting::where('tenant_id', $tenantId)->update(['is_default' => false]);
        }

        $setting = EmailSetting::create([
            'tenant_id' => $tenantId,
            'provider_name' => $validated['provider_name'],
            'display_name' => $validated['display_name'],
            'mail_mailer' => 'smtp',
            'mail_host' => $validated['mail_host'],
            'mail_port' => $validated['mail_port'],
            'mail_username' => $validated['mail_username'] ?? null,
            'mail_password' => $validated['mail_password'] ?? null,
            'mail_encryption' => $validated['mail_encryption'],
            'from_address' => $validated['from_address'],
            'from_name' => $validated['from_name'],
            'reply_to_address' => $validated['reply_to_address'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'tenant_id' => $setting->tenant_id,
            'event' => 'mail.smtp.configured',
            'description' => "Created SMTP configuration '{$setting->display_name}' ({$setting->mail_host})",
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);

        return back()->with('success', "SMTP setting '{$setting->display_name}' saved successfully.");
    }

    /**
     * Update an existing SMTP setting.
     */
    public function update(Request $request, EmailSetting $setting): RedirectResponse
    {
        $tenant = TenantContext::get();
        if ($tenant && $setting->tenant_id && $setting->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'provider_name' => ['required', 'string', 'in:custom,gmail,google_workspace,hostinger,outlook'],
            'display_name' => ['required', 'string', 'max:100'],
            'mail_host' => ['required', 'string', 'max:255'],
            'mail_port' => ['required', 'integer', 'between:1,65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['required', 'in:tls,ssl,none'],
            'from_address' => ['required', 'email', 'max:255'],
            'from_name' => ['required', 'string', 'max:100'],
            'reply_to_address' => ['nullable', 'email', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data = [
            'provider_name' => $validated['provider_name'],
            'display_name' => $validated['display_name'],
            'mail_host' => $validated['mail_host'],
            'mail_port' => $validated['mail_port'],
            'mail_username' => $validated['mail_username'] ?? null,
            'mail_encryption' => $validated['mail_encryption'],
            'from_address' => $validated['from_address'],
            'from_name' => $validated['from_name'],
            'reply_to_address' => $validated['reply_to_address'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false),
        ];

        // Only update password if a new value was explicitly entered
        if (! empty($validated['mail_password'])) {
            $data['mail_password'] = $validated['mail_password'];
        }

        if ($request->boolean('is_default')) {
            EmailSetting::where('tenant_id', $setting->tenant_id)->where('id', '!=', $setting->id)->update(['is_default' => false]);
        }

        $setting->update($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'tenant_id' => $setting->tenant_id,
            'event' => 'mail.smtp.updated',
            'description' => "Updated SMTP configuration '{$setting->display_name}'",
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);

        return back()->with('success', "SMTP configuration '{$setting->display_name}' updated.");
    }

    /**
     * Real-time socket check of an SMTP setting.
     */
    public function testConnection(Request $request, MailService $mailService): JsonResponse
    {
        $settingId = $request->input('setting_id');
        $setting = EmailSetting::findOrFail($settingId);

        $tenant = TenantContext::get();
        if ($tenant && $setting->tenant_id && $setting->tenant_id !== $tenant->id) {
            abort(403);
        }

        $result = $mailService->testConnection($setting);

        return response()->json($result);
    }

    /**
     * Send an end-to-end test email to verify credentials and sender identity.
     */
    public function sendTestEmail(Request $request, MailService $mailService): JsonResponse
    {
        $request->validate([
            'setting_id' => ['required', 'exists:email_settings,id'],
            'recipient_email' => ['required', 'email'],
        ]);

        $setting = EmailSetting::findOrFail($request->setting_id);

        $tenant = TenantContext::get();
        if ($tenant && $setting->tenant_id && $setting->tenant_id !== $tenant->id) {
            abort(403);
        }

        $result = $mailService->sendTestEmail($setting, $request->recipient_email);

        return response()->json($result);
    }

    /**
     * Toggle active status.
     */
    public function toggle(EmailSetting $setting): RedirectResponse
    {
        $tenant = TenantContext::get();
        if ($tenant && $setting->tenant_id && $setting->tenant_id !== $tenant->id) {
            abort(403);
        }

        $setting->update(['is_active' => ! $setting->is_active]);

        return back()->with('success', "SMTP setting '{$setting->display_name}' status changed.");
    }

    /**
     * Set a configuration as default.
     */
    public function setDefault(EmailSetting $setting): RedirectResponse
    {
        $tenant = TenantContext::get();
        if ($tenant && $setting->tenant_id && $setting->tenant_id !== $tenant->id) {
            abort(403);
        }

        EmailSetting::where('tenant_id', $setting->tenant_id)->update(['is_default' => false]);
        $setting->update(['is_default' => true, 'is_active' => true]);

        return back()->with('success', "'{$setting->display_name}' is now the default mail provider.");
    }

    /**
     * Remove an SMTP setting.
     */
    public function destroy(EmailSetting $setting): RedirectResponse
    {
        $tenant = TenantContext::get();
        if ($tenant && $setting->tenant_id && $setting->tenant_id !== $tenant->id) {
            abort(403);
        }

        $name = $setting->display_name;
        $setting->delete();

        return back()->with('success', "SMTP configuration '{$name}' deleted.");
    }
}
