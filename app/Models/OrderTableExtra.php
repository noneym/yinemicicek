<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTableExtra extends Model
{
    protected $fillable = ['order_table_id', 'flower_type_id', 'quantity_per_table'];

    public function orderTable(): BelongsTo
    {
        return $this->belongsTo(OrderTable::class);
    }

    public function flowerType(): BelongsTo
    {
        return $this->belongsTo(FlowerType::class);
    }
}
