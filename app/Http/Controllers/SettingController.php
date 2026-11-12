<?php

namespace App\Http\Controllers;

use App\Services\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display shop settings and user profile.
     */
    public function index(): View
    {
        $tenant = TenantContext::get();
        $user = auth()->user();

        return view('settings.index', compact('tenant', 'user'));
    }

    /**
     * Update tenant shop details and invoice customization.
     */
    public function update(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'state_code' => ['nullable', 'string', 'max:10'],
            'gstin' => ['nullable', 'string', 'max:30'],
            'default_gst_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_tax_inclusive' => ['nullable', 'boolean'],
            'upi_id' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'bank_account_no' => ['nullable', 'string', 'max:50'],
            'bank_ifsc' => ['nullable', 'string', 'max:30'],
            'bank_branch' => ['nullable', 'string', 'max:100'],
            'invoice_prefix' => ['nullable', 'string', 'max:20'],
            'invoice_title' => ['nullable', 'string', 'max:100'],
            'invoice_color' => ['nullable', 'string', 'max:20'],
            'invoice_footer_note' => ['nullable', 'string', 'max:500'],
            'terms_conditions' => ['nullable', 'string'],
            'declaration' => ['nullable', 'string'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
            'signature' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_signature' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_logo') && $tenant->logo) {
            Storage::disk('public')->delete($tenant->logo);
            $validated['logo'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            unset($validated['logo']);
        }

        if ($request->boolean('remove_signature') && $tenant->signature) {
            Storage::disk('public')->delete($tenant->signature);
            $validated['signature'] = null;
        } elseif ($request->hasFile('signature')) {
            if ($tenant->signature) {
                Storage::disk('public')->delete($tenant->signature);
            }
            $validated['signature'] = $request->file('signature')->store('signatures', 'public');
        } else {
            unset($validated['signature']);
        }

        unset($validated['remove_logo'], $validated['remove_signature']);
        $validated['is_tax_inclusive'] = $request->boolean('is_tax_inclusive');

        try {
            $tenant->update($validated);

            return back()->with('success', 'Shop settings & invoice branding saved successfully.');
        } catch (\Throwable $e) {
            Log::error('Settings update failed: '.$e->getMessage());

            return back()->withInput()->with('error', 'Unable to save settings. Please verify input values and uploaded image formats.');
        }
    }

    /**
     * Change user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Your password has been changed successfully.');
    }
}
