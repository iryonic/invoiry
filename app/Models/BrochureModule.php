<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrochureModule extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'badge',
        'icon',
        'description',
        'features',
        'sort_order',
        'is_enabled',
    ];

    protected $casts = [
        'features' => 'array',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Screenshots attached to this module.
     */
    public function screenshots(): HasMany
    {
        return $this->hasMany(BrochureScreenshot::class, 'module_id')->orderBy('sort_order');
    }
}
