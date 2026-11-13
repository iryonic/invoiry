<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrochurePlan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'period',
        'billing_note',
        'badge',
        'is_popular',
        'description',
        'features',
        'limitations',
        'sort_order',
        'is_enabled',
    ];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get HTML-safe price string with the Indian Rupee entity for PDF rendering.
     */
    public function getFormattedPriceAttribute(): string
    {
        return str_replace(['₹', 'Rs.', 'INR '], ['&#8377;', '&#8377;', '&#8377;'], (string) ($this->price ?? ''));
    }

    /**
     * Get HTML-safe period string with the Indian Rupee entity for PDF rendering.
     */
    public function getFormattedPeriodAttribute(): string
    {
        return str_replace(['₹', 'Rs.', 'INR '], ['&#8377;', '&#8377;', '&#8377;'], (string) ($this->period ?? ''));
    }
}
