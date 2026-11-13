<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BrochureScreenshot extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'caption',
        'image_path',
        'sort_order',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Module this screenshot belongs to.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(BrochureModule::class, 'module_id');
    }

    /**
     * Resolve the public or storage URL for this screenshot.
     */
    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        if (str_starts_with($this->image_path, 'storage/')) {
            return asset($this->image_path);
        }

        return Storage::url($this->image_path);
    }

    /**
     * Resolve absolute disk path for DomPDF rendering.
     */
    public function getAbsolutePathAttribute(): ?string
    {
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        $cleanPath = ltrim(str_replace('storage/', '', $this->image_path), '/');
        $storagePath = storage_path('app/public/'.$cleanPath);

        if (file_exists($storagePath)) {
            return $storagePath;
        }

        $publicPath = public_path($this->image_path);
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        return null;
    }

    /**
     * Resolve base64 data URI for reliable embedding.
     */
    public function getBase64Attribute(): ?string
    {
        $path = $this->absolute_path;
        if ($path && file_exists($path)) {
            $mime = mime_content_type($path) ?: 'image/png';
            $data = file_get_contents($path);

            return 'data:'.$mime.';base64,'.base64_encode($data);
        }

        return null;
    }
}
