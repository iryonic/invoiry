<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\EmailAutomation;
use App\Models\EmailTemplate;
use App\Services\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MailAutomationController extends Controller
{
    /**
     * Display the email automation rules management page.
     */
    public function index(): View
    {
        $tenant = TenantContext::get();
        $isSuperAdmin = auth()->user()?->isSuperAdmin() && ! session()->has('active_tenant_id');
        $tenantId = $isSuperAdmin ? null : $tenant?->id;

        $standardEvents = EmailAutomation::standardEvents();
        $templates = EmailTemplate::active()->forTenantOrGlobal($tenantId)->orderBy('name')->get();

        // Ensure automation records exist for standard events
        $automations = [];
        foreach ($standardEvents as $eventKey => $meta) {
            $defaultTemplate = $templates->firstWhere('code', $meta['default_template_code'])
                ?? $templates->first();

            $record = EmailAutomation::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'event_name' => $eventKey,
                ],
                [
                    'template_id' => $defaultTemplate?->id,
                    'is_enabled' => false,
                    'timing_days' => $eventKey === EmailAutomation::EVENT_INVOICE_DUE_REMINDER ? 3 : 0,
                    'description' => $meta['default_description'],
                ]
            );

            $automations[$eventKey] = [
                'meta' => $meta,
                'record' => $record,
            ];
        }

        return view('mail.automations.index', compact('automations', 'templates', 'tenant', 'isSuperAdmin'));
    }

    /**
     * Update automation triggers.
     */
    public function update(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get();
        $isSuperAdmin = auth()->user()?->isSuperAdmin() && ! session()->has('active_tenant_id');
        $tenantId = $isSuperAdmin ? null : $tenant?->id;

        $rules = $request->input('automations', []);

        foreach ($rules as $id => $payload) {
            $automation = EmailAutomation::where('id', $id)
                ->where('tenant_id', $tenantId)
                ->first();

            if ($automation) {
                $automation->update([
                    'is_enabled' => ! empty($payload['is_enabled']),
                    'template_id' => ! empty($payload['template_id']) ? (int) $payload['template_id'] : null,
                    'timing_days' => isset($payload['timing_days']) ? (int) $payload['timing_days'] : 0,
                ]);
            }
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'tenant_id' => $tenantId,
            'event' => 'mail.automations.updated',
            'description' => 'Updated automated email dispatch triggers',
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);

        return back()->with('success', 'Email automations updated successfully.');
    }
}
