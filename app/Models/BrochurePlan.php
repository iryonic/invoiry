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
}
