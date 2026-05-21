<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderTable extends Model
{
    protected $fillable = ['order_id', 'table_type_id', 'table_count'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function tableType(): BelongsTo
    {
        return $this->belongsTo(TableType::class);
    }

    public function extras(): HasMany
    {
        return $this->hasMany(OrderTableExtra::class);
    }
}
