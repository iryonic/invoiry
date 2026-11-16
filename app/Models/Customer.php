<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'id_proof_type',
        'id_proof_number',
        'address',
        'city',
        'state',
        'state_code',
        'gstin',
        'notes',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function repairJobs(): HasMany
    {
        return $this->hasMany(RepairJob::class)->latest();
    }

    public function totalSpent(): float
    {
        if (isset($this->invoices_sum_grand_total)) {
            return (float) $this->invoices_sum_grand_total;
        }

        return (float) $this->invoices()->sum('grand_total');
    }

    public function totalPaid(): float
    {
        if (isset($this->invoices_sum_amount_paid)) {
            return (float) $this->invoices_sum_amount_paid;
        }

        return (float) $this->invoices()->sum('amount_paid');
    }

    public function pendingBalance(): float
    {
        return max(0, $this->totalSpent() - $this->totalPaid());
    }
}
