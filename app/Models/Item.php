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
        'quantity',
        'is_device',
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
            'quantity' => 'integer',
            'is_device' => 'boolean',
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

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class)->latest();
    }

    /**
     * Scope for items currently in stock.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('status', 'in_stock')->where('quantity', '>', 0);
    }

    /**
     * Scope for items that have been sold.
     */
    public function scopeSold(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', 'sold')->orWhere('quantity', '<=', 0);
        });
    }

    /**
     * Adjust stock quantity with audit tracking.
     */
    public function adjustStock(int $newQuantity, string $type, ?string $reason = null, ?int $userId = null, ?int $invoiceId = null): StockAdjustment
    {
        $newQuantity = max(0, $newQuantity);
        $before = $this->quantity;
        $change = $newQuantity - $before;

        $this->quantity = $newQuantity;
        $this->status = $newQuantity > 0 ? 'in_stock' : 'sold';
        $this->save();

        return StockAdjustment::create([
            'tenant_id' => $this->tenant_id,
            'item_id' => $this->id,
            'user_id' => $userId ?? auth()->id(),
            'invoice_id' => $invoiceId,
            'adjustment_type' => $type,
            'quantity_change' => $change,
            'quantity_before' => $before,
            'quantity_after' => $newQuantity,
            'reason' => $reason,
        ]);
    }
}
