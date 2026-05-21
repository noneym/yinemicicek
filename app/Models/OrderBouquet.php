<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderBouquet extends Model
{
    protected $fillable = ['order_id', 'bouquet_type_id', 'bouquet_count'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function bouquetType(): BelongsTo
    {
        return $this->belongsTo(BouquetType::class);
    }
}
