<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\EmailTemplate;
use App\Services\MailService;
use App\Services\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MailTemplateController extends Controller
{
    /**
     * Display a listing of email templates.
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::get();
        $isSuperAdmin = auth()->user()?->isSuperAdmin() && ! session()->has('active_tenant_id');
        $tenantId = $isSuperAdmin ? null : $tenant?->id;

        $query = EmailTemplate::forTenantOrGlobal($tenantId);

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $templates = $query->withCount('logs')
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = [
            'all' => 'All Categories',
            'invoices' => 'Invoicing & Bills',
            'payments' => 'Payments & Receipts',
            'reminders' => 'Reminders & Khata',
            'customers' => 'Customer Care',
            'system' => 'System & Security',
            'general' => 'General Notice',
        ];

        return view('mail.templates.index', compact('templates', 'categories', 'tenant', 'isSuperAdmin'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create(): View
    {
        $tenant = TenantContext::get();
        $standardPlaceholders = EmailTemplate::standardPlaceholders();

        return view('mail.templates.create', compact('standardPlaceholders', 'tenant'));
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get();
        $isSuperAdmin = auth()->user()?->isSuperAdmin() && ! session()->has('active_tenant_id');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:100', 'alpha_dash'],
            'category' => ['required', 'string', 'in:invoices,payments,reminders,customers,system,general'],
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'body_text' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $template = EmailTemplate::create([
            'tenant_id' => $isSuperAdmin ? null : $tenant?->id,
            'name' => $validated['name'],
            'code' => Str::slug($validated['code'], '_'),
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'body_html' => $validated['body_html'],
            'body_text' => $validated['body_text'] ?? strip_tags($validated['body_html']),
            'available_placeholders' => array_keys(EmailTemplate::standardPlaceholders()),
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'tenant_id' => $template->tenant_id,
            'event' => 'mail.template.created',
            'description' => "Created email template '{$template->name}' ({$template->code})",
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);

        return redirect()->route('mail.templates.index')->with('success', "Template '{$template->name}' created successfully.");
    }

    /**
     * Show the form for editing the template.
     */
    public function edit(EmailTemplate $template): View
    {
        $tenant = TenantContext::get();
        $standardPlaceholders = EmailTemplate::standardPlaceholders();

        return view('mail.templates.edit', compact('template', 'standardPlaceholders', 'tenant'));
    }

    /**
     * Update the template in storage.
     */
    public function update(Request $request, EmailTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'in:invoices,payments,reminders,customers,system,general'],
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'body_text' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $template->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'body_html' => $validated['body_html'],
            'body_text' => $validated['body_text'] ?? strip_tags($validated['body_html']),
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'tenant_id' => $template->tenant_id,
            'event' => 'mail.template.updated',
            'description' => "Updated email template '{$template->name}'",
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);

        return redirect()->route('mail.templates.index')->with('success', "Template '{$template->name}' updated successfully.");
    }

    /**
     * Duplicate an existing template.
     */
    public function duplicate(EmailTemplate $template): RedirectResponse
    {
        $tenant = TenantContext::get();

        $copy = $template->replicate();
        $copy->tenant_id = $tenant?->id;
        $copy->name = $template->name.' (Copy)';
        $copy->code = $template->code.'_'.Str::random(4);
        $copy->is_default = false;
        $copy->save();

        return redirect()->route('mail.templates.index')->with('success', "Template duplicated as '{$copy->name}'.");
    }

    /**
     * Remove the specified template.
     */
    public function destroy(EmailTemplate $template): RedirectResponse
    {
        $name = $template->name;
        $template->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'tenant_id' => $template->tenant_id,
            'event' => 'mail.template.deleted',
            'description' => "Deleted email template '{$name}'",
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);

        return redirect()->route('mail.templates.index')->with('success', "Template '{$name}' deleted.");
    }

    /**
     * Preview template rendering with sample data.
     */
    public function preview(EmailTemplate $template, MailService $mailService): JsonResponse
    {
        $tenant = TenantContext::get();
        $placeholders = $mailService->buildPlaceholderData($tenant, null, null, [
            'customer_name' => 'John Doe',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '+91 98765 43210',
            'invoice_number' => 'INV-2026-0089',
            'invoice_date' => now()->format('d M Y'),
            'invoice_due_date' => now()->addDays(7)->format('d M Y'),
            'invoice_total' => '₹49,999.00',
            'invoice_paid' => '₹40,000.00',
            'invoice_balance_due' => '₹9,999.00',
            'payment_amount' => '₹40,000.00',
            'payment_date' => now()->format('d M Y, h:i A'),
            'payment_method' => 'UPI / Counter QR',
        ]);

        $renderedSubject = $mailService->renderPlaceholders($template->subject, $placeholders);
        $renderedHtml = $mailService->renderPlaceholders($template->body_html, $placeholders);

        return response()->json([
            'subject' => $renderedSubject,
            'body_html' => $renderedHtml,
        ]);
    }
}
