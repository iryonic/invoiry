<?php

namespace App\Models;

use Database\Factories\InvoiceItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    /** @use HasFactory<InvoiceItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'invoice_id',
        'item_id',
        'item_name',
        'storage',
        'color',
        'specs',
        'imei_1',
        'imei_2',
        'serial_number',
        'hsn_sac',
        'quantity',
        'unit_price',
        'purchase_cost',
        'tax_rate',
        'tax_amount',
        'total_price',
        'warranty_status',
        'warranty_expiry_date',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'purchase_cost' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'warranty_expiry_date' => 'date',
            'quantity' => 'integer',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Net profit margin for this line item.
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->purchase_cost > 0) {
            return (float) ($this->total_price - ($this->purchase_cost * $this->quantity));
        }

        return (float) $this->total_price;
    }
}
