<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BouquetFlower extends Model
{
    protected $fillable = ['bouquet_type_id', 'flower_type_id', 'quantity_per_bouquet'];

    public function bouquetType(): BelongsTo
    {
        return $this->belongsTo(BouquetType::class);
    }

    public function flowerType(): BelongsTo
    {
        return $this->belongsTo(FlowerType::class);
    }
}
