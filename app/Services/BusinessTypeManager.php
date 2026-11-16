<?php

namespace App\Services;

use App\Models\Tenant;

class BusinessTypeManager
{
    public const TYPE_SELLING = 'selling';

    public const TYPE_REPAIR = 'repair';

    public const TYPE_BOTH = 'both';

    /**
     * Get all available business types with labels, descriptions, and feature highlights.
     *
     * @return array<string, array{key: string, label: string, short_label: string, badge_class: string, description: string, features: list<string>}>
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_SELLING => [
                'key' => self::TYPE_SELLING,
                'label' => 'Mobile Selling & Reselling',
                'short_label' => 'Selling & Reselling',
                'badge_class' => 'bg-[#EFF8FF] text-[#175CD3] border-[#B2DDFF]',
                'description' => 'For stores selling brand new, refurbished, and used phones with IMEI & warranty tracking.',
                'features' => [
                    'Device Catalogue (Brand, Model, Storage, Color)',
                    'IMEI 1, IMEI 2 & Serial Number Management',
                    'Box Barcode Scanning & Device Stock Inward',
                    'Counter Invoicing, Cash Memos & GST Tax Bills',
                    'Device Stock Adjustments & Movement Audit',
                ],
            ],
            self::TYPE_REPAIR => [
                'key' => self::TYPE_REPAIR,
                'label' => 'Mobile Repair',
                'short_label' => 'Mobile Repair Shop',
                'badge_class' => 'bg-[#FEF0C7] text-[#B54708] border-[#FEDF89]',
                'description' => 'For mobile service centers and repair workshops managing spare parts, repair jobs, and technicians.',
                'features' => [
                    'Repair Jobs & Service Orders with Unique Job #',
                    'Device Check-in, Problem Diagnosis & Physical Condition',
                    'Spare Parts Catalogue (Screens, Batteries, ICs, Ports)',
                    'Spare Parts Stock Management & Low-Stock Alerts',
                    'Technician Assignments, Job Sheets & Repair Billing',
                ],
            ],
            self::TYPE_BOTH => [
                'key' => self::TYPE_BOTH,
                'label' => 'Both Selling & Repair',
                'short_label' => 'Selling & Repair Hub',
                'badge_class' => 'bg-[#ECFDF3] text-[#027A48] border-[#A6F4C5]',
                'description' => 'Comprehensive enterprise setup for businesses selling devices while operating a mobile repair service center.',
                'features' => [
                    'Independent Device Catalogue & Spare Parts Catalogue',
                    'Both Counter Sales Bills & Workshop Repair Orders',
                    'Dedicated Device Stock & Separate Spare Parts Stock',
                    'Shared Unified Customer Khata & Payment System',
                    'Full Dual-Mode Dashboard & Analytical Reporting',
                ],
            ],
        ];
    }

    /**
     * Check if a given business type key is valid.
     */
    public static function isValid(string $type): bool
    {
        return array_key_exists($type, self::getTypes());
    }

    /**
     * Get label for a business type.
     */
    public static function getLabel(?string $type): string
    {
        return self::getTypes()[$type ?? self::TYPE_SELLING]['label'] ?? 'Mobile Selling & Reselling';
    }

    /**
     * Check if a module is allowed for a given business type.
     */
    public static function isModuleAllowed(string $businessType, string $module): bool
    {
        return match ($businessType) {
            self::TYPE_REPAIR => ! in_array($module, ['items', 'stock'], true),
            self::TYPE_SELLING => ! in_array($module, ['repairs', 'spare_parts'], true),
            self::TYPE_BOTH => true,
            default => ! in_array($module, ['repairs', 'spare_parts'], true),
        };
    }

    /**
     * Check whether a tenant supports selling features.
     */
    public static function supportsSelling(?Tenant $tenant): bool
    {
        if (! $tenant) {
            return true;
        }

        $type = $tenant->business_type ?? self::TYPE_SELLING;

        return in_array($type, [self::TYPE_SELLING, self::TYPE_BOTH], true);
    }

    /**
     * Check whether a tenant supports repair features.
     */
    public static function supportsRepair(?Tenant $tenant): bool
    {
        if (! $tenant) {
            return false;
        }

        $type = $tenant->business_type ?? self::TYPE_SELLING;

        return in_array($type, [self::TYPE_REPAIR, self::TYPE_BOTH], true);
    }

    /**
     * Check whether a tenant is configured for both selling and repair.
     */
    public static function isBoth(?Tenant $tenant): bool
    {
        if (! $tenant) {
            return false;
        }

        return ($tenant->business_type ?? self::TYPE_SELLING) === self::TYPE_BOTH;
    }
}
