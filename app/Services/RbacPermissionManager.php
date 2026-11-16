<?php

namespace App\Services;

use App\Models\Tenant;

class RbacPermissionManager
{
    /**
     * Get all manageable modules and their granular action permissions.
     *
     * @return array<string, array{name: string, description: string, icon: string, category: string, business_types?: list<string>, is_new?: bool, actions: array<string, string>}>
     */
    public static function getModules(): array
    {
        return [
            'dashboard' => [
                'name' => 'Dashboard & Analytics',
                'description' => 'Counter sales overview, GMV statistics, and daily transaction metrics',
                'category' => 'core',
                'business_types' => ['selling', 'repair', 'both'],
                'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
                'actions' => [
                    'view' => 'View Dashboard & KPIs',
                ],
            ],
            'invoices' => [
                'name' => 'Billing & Invoices',
                'description' => 'Counter sales billing, invoice generation, PDF downloads, payments, and customer dispatch',
                'category' => 'core',
                'business_types' => ['selling', 'repair', 'both'],
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'actions' => [
                    'view' => 'View Sales Ledger & Invoices',
                    'create' => 'Create New Bill / Sell Items',
                    'edit' => 'Edit Invoices',
                    'delete' => 'Void & Delete Invoices',
                    'print' => 'Print & Download PDF Invoices',
                    'record_payments' => 'Record Invoice Payments',
                    'email' => 'Email Invoices, Receipts & Payment Reminders',
                ],
            ],
            'items' => [
                'name' => 'Device Catalogue & Inventory',
                'description' => 'Device stock management, IMEI tracking, inward purchases, and stock adjustments',
                'category' => 'inventory',
                'business_types' => ['selling', 'both'],
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'actions' => [
                    'view' => 'View Devices & Stock Catalog',
                    'create' => 'Inward Stock / Add Devices',
                    'edit' => 'Edit Device Details & Pricing',
                    'adjust_stock' => 'Adjust Stock Quantities & View Movement History',
                    'delete' => 'Delete Stock Items',
                ],
            ],
            'repairs' => [
                'name' => 'Repair Jobs & Service Orders',
                'description' => 'Customer device check-in, diagnosis, technician assignment, status tracking, job sheets, and repair billing',
                'category' => 'repair',
                'business_types' => ['repair', 'both'],
                'is_new' => true,
                'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                'actions' => [
                    'view' => 'View Repair Jobs Directory & Job Sheets',
                    'create' => 'Register New Repair Requests / Service Orders',
                    'edit' => 'Edit Repair Details, Diagnostics & Technician Notes',
                    'status_update' => 'Update Repair Workflow Statuses',
                    'assign_technician' => 'Assign & Reassign Technicians',
                    'billing' => 'Manage Spare Parts Consumption & Labour Charges',
                    'payments' => 'Record Advance & Settlement Payments',
                    'print' => 'Print Job Sheets, Intake Receipts & Gate Passes',
                    'delete' => 'Cancel & Delete Repair Records',
                ],
            ],
            'spare_parts' => [
                'name' => 'Spare Parts & Inventory',
                'description' => 'Repair components, screens, batteries, compatibility tracking, and stock adjustments',
                'category' => 'repair',
                'business_types' => ['repair', 'both'],
                'is_new' => true,
                'icon' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
                'actions' => [
                    'view' => 'View Spare Parts Catalogue & Stock Levels',
                    'create' => 'Inward / Add New Spare Parts & Components',
                    'edit' => 'Edit Spare Part Pricing & Specifications',
                    'adjust_stock' => 'Adjust Stock Quantities & Movement History',
                    'delete' => 'Delete Spare Parts',
                ],
            ],
            'customers' => [
                'name' => 'Customers & Khata',
                'description' => 'Customer directory, credit balances (Khata), and settlement history',
                'category' => 'ledger',
                'business_types' => ['selling', 'repair', 'both'],
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
                'category' => 'inventory',
                'business_types' => ['selling', 'repair', 'both'],
                'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'actions' => [
                    'view' => 'View Supplier Directory',
                    'create' => 'Register New Supplier',
                    'edit' => 'Edit Supplier Profile',
                    'delete' => 'Delete Supplier Records',
                ],
            ],
            'mail' => [
                'name' => 'Communications & Email System',
                'description' => 'Transactional email dispatch, SMTP transports, custom templates, delivery logs, and automated billing triggers',
                'category' => 'communications',
                'business_types' => ['selling', 'repair', 'both'],
                'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                'actions' => [
                    'view' => 'Access Email Dashboard, Logs & Scheduled Queue',
                    'compose' => 'Compose & Send Ad-hoc & Scheduled Emails',
                    'templates' => 'Manage & Customize Email Templates',
                    'settings' => 'Configure SMTP & Mail Provider Settings',
                    'automations' => 'Configure Automated Billing Email Triggers',
                ],
            ],
            'staff' => [
                'name' => 'Staff Management & Roles',
                'description' => 'Store cashier accounts, permissions, and operator credentials',
                'category' => 'admin',
                'business_types' => ['selling', 'repair', 'both'],
                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'actions' => [
                    'view' => 'View Staff Directory',
                    'create' => 'Create Staff User / Technician',
                    'edit' => 'Edit Staff Roles & Passwords',
                    'delete' => 'Delete Staff Accounts',
                ],
            ],
            'settings' => [
                'name' => 'Store Settings & Customization',
                'description' => 'Business profile, counter UPI QR, invoice branding, and GST rates',
                'category' => 'admin',
                'business_types' => ['selling', 'repair', 'both'],
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
     * Get default permissions mapping with permissions enabled based on business type.
     *
     * @return array<string, array<string, bool>>
     */
    public static function getDefaultPermissions(?string $businessType = null): array
    {
        $permissions = [];
        $type = $businessType ?? BusinessTypeManager::TYPE_BOTH;

        foreach (self::getModules() as $moduleKey => $module) {
            $isAllowed = BusinessTypeManager::isModuleAllowed($type, $moduleKey);
            $permissions[$moduleKey] = [];
            foreach ($module['actions'] as $actionKey => $actionLabel) {
                $permissions[$moduleKey][$actionKey] = $isAllowed;
            }
        }

        return $permissions;
    }

    /**
     * Check if a given tenant has a specific permission enabled.
     * Respects both the business-type restriction and stored permission configuration.
     */
    public static function tenantHasPermission(?Tenant $tenant, string $permission): bool
    {
        if (! $tenant) {
            return true;
        }

        $module = explode('.', $permission)[0];

        // Enforce business-type restrictions at tenant level
        if (! BusinessTypeManager::isModuleAllowed($tenant->business_type ?? BusinessTypeManager::TYPE_SELLING, $module)) {
            return false;
        }

        if (! is_array($tenant->permissions) || empty($tenant->permissions)) {
            return true;
        }

        $parts = explode('.', $permission);
        if (count($parts) === 2) {
            [$mod, $act] = $parts;

            return (bool) ($tenant->permissions[$mod][$act] ?? true);
        }

        if (count($parts) === 1) {
            $mod = $parts[0];
            if (isset($tenant->permissions[$mod])) {
                if (is_array($tenant->permissions[$mod])) {
                    return (bool) ($tenant->permissions[$mod]['view'] ?? true);
                }

                return (bool) $tenant->permissions[$mod];
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
