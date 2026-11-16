<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffController extends Controller
{
    /**
     * Display staff & team members for the active tenant.
     */
    public function index(): View
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 403, 'Tenant context not found.');

        $staff = User::where('tenant_id', $tenant->id)
            ->where('role', '!=', 'super_admin')
            ->orderBy('role')
            ->latest()
            ->paginate(15);

        return view('staff.index', compact('tenant', 'staff'));
    }

    /**
     * Store a new staff member.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant, 403, 'Tenant context not found.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:tenant_admin,cashier,technician'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        AuditLog::record('staff.created', "Created staff member {$user->name} ({$user->role})", $tenant->id);

        return back()->with('success', "Staff member \"{$user->name}\" added successfully!");
    }

    /**
     * Update an existing staff member.
     */
    public function update(Request $request, User $staff): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $staff->tenant_id === $tenant->id, 403, 'Unauthorized.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:tenant_admin,cashier,technician'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $staff->name = $validated['name'];
        $staff->email = $validated['email'];
        $staff->phone = $validated['phone'];
        $staff->role = $validated['role'];

        if (! empty($validated['password'])) {
            $staff->password = Hash::make($validated['password']);
        }

        $staff->save();

        AuditLog::record('staff.updated', "Updated staff member {$staff->name}", $tenant->id);

        return back()->with('success', "Staff member \"{$staff->name}\" updated successfully!");
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(User $staff): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $staff->tenant_id === $tenant->id, 403, 'Unauthorized.');

        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $staff->update(['is_active' => ! $staff->is_active]);
        $status = $staff->is_active ? 'activated' : 'deactivated';

        AuditLog::record('staff.status_toggled', "Staff {$staff->name} {$status}", $tenant->id);

        return back()->with('success', "Account has been {$status}.");
    }

    /**
     * Delete a staff user.
     */
    public function destroy(User $staff): RedirectResponse
    {
        $tenant = TenantContext::get();
        abort_unless($tenant && $staff->tenant_id === $tenant->id, 403, 'Unauthorized.');

        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $staff->name;
        $staff->delete();

        AuditLog::record('staff.deleted', "Deleted staff member {$name}", $tenant->id);

        return back()->with('success', "Staff member \"{$name}\" deleted.");
    }
}
