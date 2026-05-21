<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FlowerType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color', 'unit', 'image_path', 'notes'];

    protected static function booted(): void
    {
        static::saving(function (self $flower) {
            if (empty($flower->slug)) {
                $base = Str::slug($flower->name);
                $slug = $base;
                $i = 2;
                while (self::where('slug', $slug)->where('id', '!=', $flower->id)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $flower->slug = $slug;
            }
        });
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}
