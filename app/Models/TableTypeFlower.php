<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableTypeFlower extends Model
{
    protected $fillable = ['table_type_id', 'flower_type_id', 'quantity_per_table'];

    public function tableType(): BelongsTo
    {
        return $this->belongsTo(TableType::class);
    }

    public function flowerType(): BelongsTo
    {
        return $this->belongsTo(FlowerType::class);
    }
}
