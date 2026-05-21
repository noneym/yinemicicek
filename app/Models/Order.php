<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'notes'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(OrderTable::class);
    }

    public function bouquets(): HasMany
    {
        return $this->hasMany(OrderBouquet::class);
    }

    public function looseFlowers(): HasMany
    {
        return $this->hasMany(OrderLooseFlower::class);
    }

    /**
     * Birim bazlı toplam çiçek envanteri.
     * Format: [ flower_type_id => [ 'flower' => FlowerType, 'totals' => [ unit => qty ] ] ]
     */
    public function aggregateFlowerInventory(): array
    {
        $this->loadMissing([
            'tables.tableType.flowerLines.flowerType',
            'tables.extras.flowerType',
            'bouquets.bouquetType.flowerLines.flowerType',
            'looseFlowers.flowerType',
        ]);

        $totals = [];

        $add = function (FlowerType $flower, float $qty, ?string $unitOverride = null) use (&$totals) {
            $unit = $unitOverride ?: ($flower->unit ?: 'adet');
            $id = $flower->id;
            if (!isset($totals[$id])) {
                $totals[$id] = ['flower' => $flower, 'totals' => []];
            }
            $totals[$id]['totals'][$unit] = ($totals[$id]['totals'][$unit] ?? 0) + $qty;
        };

        foreach ($this->tables as $orderTable) {
            $count = $orderTable->table_count;
            if ($orderTable->tableType) {
                foreach ($orderTable->tableType->flowerLines as $line) {
                    if ($line->flowerType) {
                        $add($line->flowerType, $line->quantity_per_table * $count);
                    }
                }
            }
            foreach ($orderTable->extras as $extra) {
                if ($extra->flowerType) {
                    $add($extra->flowerType, $extra->quantity_per_table * $count);
                }
            }
        }

        foreach ($this->bouquets as $orderBouquet) {
            $count = $orderBouquet->bouquet_count;
            if ($orderBouquet->bouquetType) {
                foreach ($orderBouquet->bouquetType->flowerLines as $line) {
                    if ($line->flowerType) {
                        $add($line->flowerType, $line->quantity_per_bouquet * $count);
                    }
                }
            }
        }

        foreach ($this->looseFlowers as $loose) {
            if ($loose->flowerType) {
                $add($loose->flowerType, (float) $loose->quantity, $loose->unit_override);
            }
        }

        uasort($totals, fn ($a, $b) => strcasecmp($a['flower']->name, $b['flower']->name));

        return $totals;
    }
}
