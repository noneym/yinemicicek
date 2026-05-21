<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class BouquetType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'image_path'];

    public function flowers(): BelongsToMany
    {
        return $this->belongsToMany(FlowerType::class, 'bouquet_flowers')
            ->withPivot('quantity_per_bouquet', 'id')
            ->withTimestamps();
    }

    public function flowerLines(): HasMany
    {
        return $this->hasMany(BouquetFlower::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}
