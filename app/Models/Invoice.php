<?php

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'customer_id',
        'user_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'discount',
        'tax_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'grand_total',
        'amount_paid',
        'payment_method',
        'payment_status',
        'is_gst',
        'tax_rate',
        'is_tax_inclusive',
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
            'invoice_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'cgst_amount' => 'decimal:2',
            'sgst_amount' => 'decimal:2',
            'igst_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'is_tax_inclusive' => 'boolean',
            'is_gst' => 'boolean',
        ];
    }

    /**
     * Check if invoice is inter-state (IGST applicable).
     */
    public function isInterState(): bool
    {
        return (float) $this->igst_amount > 0;
    }

    /**
     * Get applicable CGST percentage rate.
     */
    public function getCgstRateAttribute(): float
    {
        if (! $this->is_gst || $this->isInterState()) {
            return 0.0;
        }

        return round((float) $this->tax_rate / 2, 2);
    }

    /**
     * Get applicable SGST percentage rate.
     */
    public function getSgstRateAttribute(): float
    {
        if (! $this->is_gst || $this->isInterState()) {
            return 0.0;
        }

        return round((float) $this->tax_rate / 2, 2);
    }

    /**
     * Get applicable IGST percentage rate.
     */
    public function getIgstRateAttribute(): float
    {
        if (! $this->is_gst || ! $this->isInterState()) {
            return 0.0;
        }

        return (float) $this->tax_rate;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getAmountDueAttribute(): float
    {
        return max(0, (float) $this->grand_total - (float) $this->amount_paid);
    }

    /**
     * Dynamic UPI Deep-link for mobile app / QR code generation.
     */
    public function getUpiQrStringAttribute(): ?string
    {
        if (! $this->tenant || ! $this->tenant->upi_id) {
            return null;
        }

        $vpa = urlencode($this->tenant->upi_id);
        $name = urlencode($this->tenant->name);
        $amount = number_format((float) $this->grand_total, 2, '.', '');
        $note = urlencode("Bill {$this->invoice_number}");

        return "upi://pay?pa={$vpa}&pn={$name}&am={$amount}&tn={$note}&cu=INR";
    }

    /**
     * Convert numeric grand total to words (Indian numbering system).
     */
    public function amountInWords(): string
    {
        $amount = round((float) $this->grand_total);
        if ($amount == 0) {
            return 'Zero Rupees Only';
        }

        $ones = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
        ];

        $tens = [
            2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
            6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety',
        ];

        $convertNumber = function ($num) use (&$convertNumber, $ones, $tens): string {
            if ($num == 0) {
                return '';
            }
            if ($num < 20) {
                return $ones[$num];
            }
            if ($num < 100) {
                return $tens[(int) ($num / 10)].($num % 10 != 0 ? ' '.$ones[$num % 10] : '');
            }
            if ($num < 1000) {
                return $ones[(int) ($num / 100)].' Hundred'.($num % 100 != 0 ? ' '.$convertNumber($num % 100) : '');
            }
            if ($num < 100000) {
                return $convertNumber((int) ($num / 1000)).' Thousand'.($num % 1000 != 0 ? ' '.$convertNumber($num % 1000) : '');
            }
            if ($num < 10000000) {
                return $convertNumber((int) ($num / 100000)).' Lakh'.($num % 100000 != 0 ? ' '.$convertNumber($num % 100000) : '');
            }

            return $convertNumber((int) ($num / 10000000)).' Crore'.($num % 10000000 != 0 ? ' '.$convertNumber($num % 10000000) : '');
        };

        return trim($convertNumber($amount)).' Rupees Only';
    }
}
