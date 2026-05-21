<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLooseFlower extends Model
{
    protected $fillable = ['order_id', 'flower_type_id', 'quantity', 'unit_override', 'notes'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function flowerType(): BelongsTo
    {
        return $this->belongsTo(FlowerType::class);
    }
}
