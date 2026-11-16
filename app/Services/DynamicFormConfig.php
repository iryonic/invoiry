<?php

namespace App\Services;

use App\Models\Tenant;

class DynamicFormConfig
{
    /**
     * Get all configurable application forms and their field definitions.
     *
     * @return array<string, array{name: string, description: string, fields: array<string, array{label: string, system: bool, default_visible: bool, default_required: bool, hint?: string}>}>
     */
    public static function getForms(): array
    {
        return [
            'invoice' => [
                'name' => 'Create Bill & Invoice Form',
                'description' => 'Fields displayed when cashier/admin generates a retail bill at the counter',
                'fields' => [
                    'customer_name' => [
                        'label' => 'Customer Name',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Core buyer identifier; required for invoice creation',
                    ],
                    'customer_phone' => [
                        'label' => 'Customer Phone Number',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Used for WhatsApp receipt dispatch and Khata lookup',
                    ],
                    'customer_email' => [
                        'label' => 'Customer Email Address',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'For electronic email billing notifications',
                    ],
                    'customer_address' => [
                        'label' => 'Customer Street Address',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Customer physical residence or store location',
                    ],
                    'customer_state' => [
                        'label' => 'Customer State & GST State Code',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Used for Inter-State vs Intra-State GST resolution',
                    ],
                    'invoice_date' => [
                        'label' => 'Invoice Billing Date',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Defaults to today\'s transaction date',
                    ],
                    'due_date' => [
                        'label' => 'Payment Due Date',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'For credit sales and customer Khata tracking',
                    ],
                    'payment_method' => [
                        'label' => 'Payment Method Selector',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Cash, UPI, Card, or Bank Wire',
                    ],
                    'storage_color' => [
                        'label' => 'Item Storage & Color Variants',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'e.g. 256GB / Space Black fields on line items',
                    ],
                    'imei_serial' => [
                        'label' => 'IMEI 1, IMEI 2 & Serial Numbers',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Device hardware tracking identifiers',
                    ],
                    'warranty' => [
                        'label' => 'Warranty Status & Validity Period',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Brand / Store warranty and expiration date',
                    ],
                    'is_gst' => [
                        'label' => 'GST Tax Calculation Switch',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Allows switching between Tax Invoice and Cash Memo',
                    ],
                    'tax_rate' => [
                        'label' => 'Custom GST Tax Rate % Input',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Override standard default tax rate per invoice',
                    ],
                    'tax_inclusive_toggle' => [
                        'label' => 'Tax Inclusive / Exclusive Switch',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Toggle MRP-inclusive pricing vs adding tax on top',
                    ],
                    'discount' => [
                        'label' => 'Invoice Discount Input',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Lump sum discount deduction on bill',
                    ],
                    'amount_paid' => [
                        'label' => 'Partial Payment / Amount Paid Input',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Allows recording partial down-payments and credit dues',
                    ],
                    'notes' => [
                        'label' => 'Customer Invoice Notes & Remarks',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Public remarks printed on customer bill',
                    ],
                ],
            ],
            'customer' => [
                'name' => 'Add Customer Form',
                'description' => 'Inputs available when registering a new retail customer or Khata account',
                'fields' => [
                    'name' => [
                        'label' => 'Customer Full Name',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'System-critical customer primary key',
                    ],
                    'phone' => [
                        'label' => 'Phone / Mobile Number',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Primary communication identifier',
                    ],
                    'email' => [
                        'label' => 'Email Address',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Electronic receipt delivery',
                    ],
                    'address' => [
                        'label' => 'Street Address / Locality',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Physical home or shop address',
                    ],
                    'state' => [
                        'label' => 'State & State Code',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Customer jurisdiction for GST classification',
                    ],
                    'gstin' => [
                        'label' => 'Customer GSTIN',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'For B2B tax credit / commercial buyers',
                    ],
                ],
            ],
            'item' => [
                'name' => ' Add Device Form',
                'description' => 'Inputs available when bringing in new inventory or purchased hardware',
                'fields' => [
                    'item_name' => [
                        'label' => 'Device Model / Item Name',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Core inventory item title',
                    ],
                    'brand' => [
                        'label' => 'Brand / Manufacturer',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Apple, Samsung, Xiaomi, etc.',
                    ],
                    'storage' => [
                        'label' => 'Storage Capacity',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => '128GB, 256GB, 512GB, etc.',
                    ],
                    'color' => [
                        'label' => 'Color Variant',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Space Grey, Midnight Blue, etc.',
                    ],
                    'condition' => [
                        'label' => 'Device Condition (New/Used/Refurbished)',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Cosmetic and operational grading',
                    ],
                    'imei_1' => [
                        'label' => 'Primary IMEI 1',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Hardware 15-digit TAC identifier',
                    ],
                    'imei_2' => [
                        'label' => 'Secondary IMEI 2 (Dual SIM)',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Secondary SIM slot identifier',
                    ],
                    'serial_number' => [
                        'label' => 'Serial Number / Part No.',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Manufacturer serial code',
                    ],
                    'specs' => [
                        'label' => 'Condition Notes & Specifications',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Battery health, accessories included, cosmetic notes',
                    ],
                    'purchase_price' => [
                        'label' => 'Inward Purchase Cost (₹)',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Wholesale cost or purchase price',
                    ],
                    'selling_price' => [
                        'label' => 'Expected Selling Price / MRP (₹)',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Base retail asking price',
                    ],
                    'purchase_date' => [
                        'label' => 'Inward Purchase Date',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'When device was brought into inventory',
                    ],
                    'payment_method' => [
                        'label' => 'Payment Method to Vendor',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Cash, UPI, Bank Transfer to supplier',
                    ],
                    'warranty_status' => [
                        'label' => 'Warranty Coverage & Expiry',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Store or brand warranty coverage',
                    ],
                    'supplier' => [
                        'label' => 'Supplier / Vendor Origin',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Distributor or previous owner origin',
                    ],
                ],
            ],
            'supplier' => [
                'name' => 'Add Supplier Form',
                'description' => 'Inputs available when adding new inventory suppliers',
                'fields' => [
                    'name' => [
                        'label' => 'Supplier / Contact Person Name',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Contact representative name',
                    ],
                    'company_name' => [
                        'label' => 'Distributor / Company Name',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Wholesale trade name',
                    ],
                    'phone' => [
                        'label' => 'Phone Number',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Supplier mobile or landline',
                    ],
                    'gstin' => [
                        'label' => 'Supplier GSTIN',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Vendor tax registration identifier',
                    ],
                    'address' => [
                        'label' => 'Supplier Address',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Warehouse or office location',
                    ],
                ],
            ],
            'staff' => [
                'name' => 'Staff Form',
                'description' => 'Inputs available when adding cashiers or store staff',
                'fields' => [
                    'name' => [
                        'label' => 'Staff Full Name',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Employee identity',
                    ],
                    'email' => [
                        'label' => 'Email Address / Login ID',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Login credential',
                    ],
                    'phone' => [
                        'label' => 'Phone Number',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Staff mobile contact',
                    ],
                    'role' => [
                        'label' => 'Assigned Role',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Cashier or Store Admin role',
                    ],
                    'password' => [
                        'label' => 'Initial Password',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Access credential',
                    ],
                ],
            ],
            'spare_part' => [
                'name' => 'Add Spare Part Form',
                'description' => 'Inputs available when adding repair spare parts and hardware components',
                'fields' => [
                    'name' => [
                        'label' => 'Spare Part Name',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'e.g. iPhone 13 Pro OLED Screen Panel, Samsung S22 Charging Sub-board',
                    ],
                    'category' => [
                        'label' => 'Part Category',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Screen/Display, Battery, Camera, Charging Port, etc.',
                    ],
                    'brand' => [
                        'label' => 'Brand / Manufacturer',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Apple, Samsung, OEM, Generic',
                    ],
                    'compatible_models' => [
                        'label' => 'Compatible Models',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'e.g. iPhone 13, iPhone 14',
                    ],
                    'sku' => [
                        'label' => 'SKU / Part Code',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Unique internal catalogue code',
                    ],
                    'barcode' => [
                        'label' => 'Barcode / EAN',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Scannable barcode string',
                    ],
                    'specifications' => [
                        'label' => 'Specifications & Notes',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Quality grading, warranty, IC details',
                    ],
                    'purchase_price' => [
                        'label' => 'Purchase Cost (₹)',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Inward procurement cost',
                    ],
                    'selling_price' => [
                        'label' => 'Selling Price / Price in Repair (₹)',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Base retail / repair billing price',
                    ],
                    'stock_quantity' => [
                        'label' => 'Initial Stock Quantity',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Number of units added',
                    ],
                    'min_stock_level' => [
                        'label' => 'Minimum Stock Alert Threshold',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Alerts when stock falls to this level',
                    ],
                    'supplier' => [
                        'label' => 'Supplier / Vendor',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Wholesale vendor origin',
                    ],
                ],
            ],
            'repair_job' => [
                'name' => 'Repair Job Form',
                'description' => 'Inputs available when registering customer device repair requests',
                'fields' => [
                    'device_brand' => [
                        'label' => 'Device Brand',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Apple, Samsung, Xiaomi, Vivo, etc.',
                    ],
                    'device_model' => [
                        'label' => 'Device Model',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Core device identifier, e.g. iPhone 14 Pro',
                    ],
                    'imei' => [
                        'label' => 'Device IMEI Number',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Optional TAC hardware identifier',
                    ],
                    'serial_number' => [
                        'label' => 'Serial Number',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Manufacturer serial code',
                    ],
                    'device_color' => [
                        'label' => 'Device Colour',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Cosmetic color variant',
                    ],
                    'device_password' => [
                        'label' => 'Screen Lock / Passcode',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'PIN or pattern for testing functionality',
                    ],
                    'device_condition' => [
                        'label' => 'Physical Condition at Intake',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Scratches, cracked screen, missing buttons, power state',
                    ],
                    'accessories_received' => [
                        'label' => 'Accessories Received',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'SIM tray, charger, memory card, case',
                    ],
                    'reported_problem' => [
                        'label' => 'Reported Issue / Problem',
                        'system' => true,
                        'default_visible' => true,
                        'default_required' => true,
                        'hint' => 'Customer problem description',
                    ],
                    'diagnosis_notes' => [
                        'label' => 'Initial Diagnosis Notes',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Counter check-in inspection notes',
                    ],
                    'technician' => [
                        'label' => 'Assign Technician',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Staff technician assigned to repair',
                    ],
                    'estimated_cost' => [
                        'label' => 'Estimated Repair Cost (₹)',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Estimated quote for the customer',
                    ],
                    'expected_completion_date' => [
                        'label' => 'Expected Delivery Date',
                        'system' => false,
                        'default_visible' => true,
                        'default_required' => false,
                        'hint' => 'Target completion date',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get default form field configuration with all fields enabled.
     *
     * @return array<string, array<string, array{visible: bool, required: bool}>>
     */
    public static function getDefaultConfig(): array
    {
        $config = [];
        foreach (self::getForms() as $formKey => $form) {
            $config[$formKey] = [];
            foreach ($form['fields'] as $fieldKey => $field) {
                $config[$formKey][$fieldKey] = [
                    'visible' => $field['default_visible'],
                    'required' => $field['default_required'],
                ];
            }
        }

        return $config;
    }

    /**
     * Check if a field is visible on a given form for the specified tenant.
     */
    public static function isFieldVisible(?Tenant $tenant, string $form, string $field): bool
    {
        $tenant = $tenant ?? TenantContext::get();

        // Business-type awareness
        if ($tenant) {
            if ($tenant->isRepairOnly()) {
                // In repair-only businesses, device-only fields are not applicable
                if ($form === 'item' && in_array($field, ['imei_1', 'imei_2', 'serial_number', 'condition', 'storage', 'color'], true)) {
                    return false;
                }
            } elseif ($tenant->isSellingOnly()) {
                // In selling-only businesses, repair forms are not applicable
                if (in_array($form, ['spare_part', 'repair_job'], true)) {
                    return false;
                }
            }
        }

        // System-critical fields cannot be hidden
        $forms = self::getForms();
        if (isset($forms[$form]['fields'][$field]['system']) && $forms[$form]['fields'][$field]['system']) {
            return true;
        }

        if (! $tenant || ! is_array($tenant->form_fields)) {
            return true;
        }

        // 1. Direct form field check: [form][field]
        if (isset($tenant->form_fields[$form][$field])) {
            $val = $tenant->form_fields[$form][$field];
            if (is_array($val)) {
                if (array_key_exists('visible', $val)) {
                    return (bool) $val['visible'];
                }
            } elseif (is_bool($val) || is_numeric($val)) {
                return (bool) $val;
            }
        }

        // 2. Cross-form customer field synchronization
        // If customer_phone or customer_address is disabled in customer form or invoice form, synchronize visibility
        if ($form === 'invoice' && str_starts_with($field, 'customer_')) {
            $customerKey = str_replace('customer_', '', $field);
            if (isset($tenant->form_fields['customer'][$customerKey])) {
                $val = $tenant->form_fields['customer'][$customerKey];
                $custVis = is_array($val) ? ($val['visible'] ?? true) : (bool) $val;
                if (! $custVis) {
                    return false;
                }
            }
        } elseif ($form === 'customer') {
            $invoiceKey = 'customer_'.$field;
            if (isset($tenant->form_fields['invoice'][$invoiceKey])) {
                $val = $tenant->form_fields['invoice'][$invoiceKey];
                $invVis = is_array($val) ? ($val['visible'] ?? true) : (bool) $val;
                if (! $invVis) {
                    return false;
                }
            }
        }

        // 3. Flat key fallback: [field]
        if (isset($tenant->form_fields[$field])) {
            $val = $tenant->form_fields[$field];
            if (is_array($val)) {
                return (bool) ($val['visible'] ?? true);
            }

            return (bool) $val;
        }

        return true;
    }

    /**
     * Check if a field is marked required on a given form for the specified tenant.
     */
    public static function isFieldRequired(?Tenant $tenant, string $form, string $field): bool
    {
        $tenant = $tenant ?? TenantContext::get();

        $forms = self::getForms();
        $isSystem = $forms[$form]['fields'][$field]['system'] ?? false;
        $defaultReq = $forms[$form]['fields'][$field]['default_required'] ?? false;

        if ($isSystem) {
            return $defaultReq;
        }

        // If the field is hidden, it cannot be required
        if (! self::isFieldVisible($tenant, $form, $field)) {
            return false;
        }

        if (! $tenant || ! is_array($tenant->form_fields)) {
            return $defaultReq;
        }

        if (isset($tenant->form_fields[$form][$field])) {
            $val = $tenant->form_fields[$form][$field];
            if (is_array($val)) {
                return (bool) ($val['required'] ?? $defaultReq);
            }

            return (bool) $val;
        }

        return $defaultReq;
    }

    /**
     * Normalize form configuration from request input.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, array<string, array{visible: bool, required: bool}>>
     */
    public static function normalizeFromRequest(array $input): array
    {
        $normalized = [];
        foreach (self::getForms() as $formKey => $form) {
            $normalized[$formKey] = [];
            foreach ($form['fields'] as $fieldKey => $field) {
                $isSystem = $field['system'];

                // System fields are always visible
                $visible = $isSystem ? true : (bool) ($input[$formKey][$fieldKey]['visible'] ?? false);
                $required = $isSystem ? $field['default_required'] : (bool) ($input[$formKey][$fieldKey]['required'] ?? false);

                // If not visible, it cannot be required
                if (! $visible) {
                    $required = false;
                }

                $normalized[$formKey][$fieldKey] = [
                    'visible' => $visible,
                    'required' => $required,
                ];
            }
        }

        return $normalized;
    }
}
