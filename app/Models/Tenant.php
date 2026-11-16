<?php

namespace App\Models;

use App\Services\BusinessTypeManager;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'state_code',
        'gstin',
        'default_gst_rate',
        'is_tax_inclusive',
        'upi_id',
        'bank_name',
        'bank_account_no',
        'bank_ifsc',
        'bank_branch',
        'logo',
        'signature',
        'invoice_prefix',
        'invoice_title',
        'invoice_color',
        'invoice_footer_note',
        'invoice_counter',
        'terms_conditions',
        'declaration',
        'plan',
        'business_type',
        'repair_job_prefix',
        'repair_job_counter',
        'expires_at',
        'is_active',
        'permissions',
        'form_fields',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_tax_inclusive' => 'boolean',
            'default_gst_rate' => 'decimal:2',
            'expires_at' => 'date',
            'invoice_counter' => 'integer',
            'repair_job_counter' => 'integer',
            'permissions' => 'array',
            'form_fields' => 'array',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get web URL for store logo.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return asset('storage/'.$this->logo);
        }

        return null;
    }

    /**
     * Get web URL for signature / stamp.
     */
    public function getSignatureUrlAttribute(): ?string
    {
        if ($this->signature && Storage::disk('public')->exists($this->signature)) {
            return asset('storage/'.$this->signature);
        }

        return null;
    }

    /**
     * Get embedded base64 string for PDF rendering.
     */
    public function getLogoBase64Attribute(): ?string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            $path = storage_path('app/public/'.$this->logo);
            if (file_exists($path)) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mime = match ($ext) {
                    'jpg', 'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                    default => 'image/png',
                };
                $data = file_get_contents($path);

                return 'data:'.$mime.';base64,'.base64_encode($data);
            }
        }

        return null;
    }

    /**
     * Get embedded base64 string for signature rendering.
     */
    public function getSignatureBase64Attribute(): ?string
    {
        if ($this->signature && Storage::disk('public')->exists($this->signature)) {
            $path = storage_path('app/public/'.$this->signature);
            if (file_exists($path)) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mime = match ($ext) {
                    'jpg', 'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                    default => 'image/png',
                };
                $data = file_get_contents($path);

                return 'data:'.$mime.';base64,'.base64_encode($data);
            }
        }

        return null;
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function spareParts(): HasMany
    {
        return $this->hasMany(SparePart::class);
    }

    public function repairJobs(): HasMany
    {
        return $this->hasMany(RepairJob::class);
    }

    public function sparePartStockMovements(): HasMany
    {
        return $this->hasMany(SparePartStockMovement::class);
    }

    public function supportsSelling(): bool
    {
        return BusinessTypeManager::supportsSelling($this);
    }

    public function supportsRepair(): bool
    {
        return BusinessTypeManager::supportsRepair($this);
    }

    public function isSellingOnly(): bool
    {
        return ($this->business_type ?? BusinessTypeManager::TYPE_SELLING) === BusinessTypeManager::TYPE_SELLING;
    }

    public function isRepairOnly(): bool
    {
        return ($this->business_type ?? BusinessTypeManager::TYPE_SELLING) === BusinessTypeManager::TYPE_REPAIR;
    }

    public function isBoth(): bool
    {
        return BusinessTypeManager::isBoth($this);
    }

    public function getBusinessTypeNameAttribute(): string
    {
        return BusinessTypeManager::getLabel($this->business_type);
    }

    /**
     * Generate the next formatted invoice number and increment counter.
     */
    public function generateNextInvoiceNumber(): string
    {
        $prefix = $this->invoice_prefix ?: 'INV-';
        $number = str_pad((string) $this->invoice_counter, 5, '0', STR_PAD_LEFT);
        $this->increment('invoice_counter');

        return "{$prefix}{$number}";
    }

    /**
     * Generate the next formatted repair job number and increment counter.
     */
    public function generateNextRepairJobNumber(): string
    {
        $prefix = $this->repair_job_prefix ?: 'REP-';
        $number = str_pad((string) ($this->repair_job_counter ?: 1), 5, '0', STR_PAD_LEFT);
        $this->increment('repair_job_counter');

        return "{$prefix}{$number}";
    }
}
