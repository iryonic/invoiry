<?php

namespace App\Services;

use App\Models\Tenant;

class RbacPermissionManager
{
    /**
     * Get all manageable modules and their granular action permissions.
     *
     * @return array<string, array{name: string, description: string, icon: string, actions: array<string, string>}>
     */
    public static function getModules(): array
    {
        return [
            'dashboard' => [
                'name' => 'Dashboard & Analytics',
                'description' => 'Counter sales overview, GMV statistics, and daily transaction metrics',
                'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
                'actions' => [
                    'view' => 'View Dashboard & KPIs',
                ],
            ],
            'invoices' => [
                'name' => 'Billing & Invoices',
                'description' => 'Counter sales billing, invoice generation, PDF downloads, and payments',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'actions' => [
                    'view' => 'View Sales Ledger & Invoices',
                    'create' => 'Create New Bill / Sell Items',
                    'edit' => 'Edit Invoices',
                    'delete' => 'Void & Delete Invoices',
                    'print' => 'Print & Download PDF Invoices',
                    'record_payments' => 'Record Invoice Payments',
                ],
            ],
            'items' => [
                'name' => 'Products & Inventory',
                'description' => 'Device stock management, IMEI tracking, and inward inventory purchases',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'actions' => [
                    'view' => 'View Devices & Stock Catalog',
                    'create' => 'Inward Stock / Add Devices',
                    'edit' => 'Edit Device Details & Pricing',
                    'adjust_stock' => 'Adjust Stock Quantities & View Movement History',
                    'delete' => 'Delete Stock Items',
                ],
            ],
            'customers' => [
                'name' => 'Customers & Khata',
                'description' => 'Customer directory, credit balances (Khata), and settlement history',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                'actions' => [
                    'view' => 'View Customers & Ledger',
                    'create' => 'Register New Customer',
                    'edit' => 'Edit Customer Information',
                    'delete' => 'Delete Customer Records',
                    'payments' => 'Record Credit Settlements',
                ],
            ],
            'suppliers' => [
                'name' => 'Suppliers & Vendors',
                'description' => 'Wholesale vendors, distributors, and inward purchase sources',
                'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'actions' => [
                    'view' => 'View Supplier Directory',
                    'create' => 'Register New Supplier',
                    'edit' => 'Edit Supplier Profile',
                    'delete' => 'Delete Supplier Records',
                ],
            ],
            'staff' => [
                'name' => 'Staff Management & Roles',
                'description' => 'Store cashier accounts, permissions, and operator credentials',
                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'actions' => [
                    'view' => 'View Staff Directory',
                    'create' => 'Create Cashier / Staff User',
                    'edit' => 'Edit Staff Roles & Passwords',
                    'delete' => 'Delete Staff Accounts',
                ],
            ],
            'settings' => [
                'name' => 'Store Settings & Customization',
                'description' => 'Business profile, counter UPI QR, invoice branding, and GST rates',
                'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
                'actions' => [
                    'view' => 'Access Settings Page',
                    'edit' => 'Edit Store Identity & Address',
                    'branding' => 'Customize Invoice Branding & Logo',
                    'gst' => 'Configure Store GST & Tax Rules',
                    'banking' => 'Configure Bank & Counter UPI QR',
                ],
            ],
        ];
    }

    /**
     * Get default permissions mapping with all permissions set to true.
     *
     * @return array<string, array<string, bool>>
     */
    public static function getDefaultPermissions(): array
    {
        $permissions = [];
        foreach (self::getModules() as $moduleKey => $module) {
            $permissions[$moduleKey] = [];
            foreach ($module['actions'] as $actionKey => $actionLabel) {
                $permissions[$moduleKey][$actionKey] = true;
            }
        }

        return $permissions;
    }

    /**
     * Check if a given tenant has a specific permission enabled.
     */
    public static function tenantHasPermission(?Tenant $tenant, string $permission): bool
    {
        if (! $tenant) {
            return true;
        }

        if (! is_array($tenant->permissions) || empty($tenant->permissions)) {
            return true;
        }

        $parts = explode('.', $permission);
        if (count($parts) === 2) {
            [$module, $action] = $parts;

            return (bool) ($tenant->permissions[$module][$action] ?? true);
        }

        if (count($parts) === 1) {
            $module = $parts[0];
            if (isset($tenant->permissions[$module])) {
                if (is_array($tenant->permissions[$module])) {
                    return (bool) ($tenant->permissions[$module]['view'] ?? true);
                }

                return (bool) $tenant->permissions[$module];
            }
        }

        return true;
    }

    /**
     * Normalize posted form inputs into structured permissions array.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, array<string, bool>>
     */
    public static function normalizeFromRequest(array $input): array
    {
        $normalized = [];
        foreach (self::getModules() as $moduleKey => $module) {
            $normalized[$moduleKey] = [];
            foreach ($module['actions'] as $actionKey => $actionLabel) {
                $postedVal = $input[$moduleKey][$actionKey] ?? null;
                $normalized[$moduleKey][$actionKey] = (bool) $postedVal;
            }
        }

        return $normalized;
    }
}
