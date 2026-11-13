<?php

namespace App\Models;

use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'item_name',
        'brand',
        'category',
        'storage',
        'color',
        'specs',
        'imei_1',
        'imei_2',
        'serial_number',
        'condition',
        'status',
        'purchase_price',
        'selling_price',
        'warranty_status',
        'warranty_expiry_date',
        'purchase_date',
        'payment_method',
        'remark',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'purchase_date' => 'date',
            'warranty_expiry_date' => 'date',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function invoiceItem(): HasOne
    {
        return $this->hasOne(InvoiceItem::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Scope for items currently in stock.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('status', 'in_stock');
    }

    /**
     * Scope for items that have been sold.
     */
    public function scopeSold(Builder $query): Builder
    {
        return $query->where('status', 'sold');
    }
}
