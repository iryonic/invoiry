<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrochureGeneration extends Model
{
    protected $fillable = [
        'version',
        'file_path',
        'file_size',
        'generated_by',
        'notes',
    ];

    /**
     * User who initiated this generation.
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
