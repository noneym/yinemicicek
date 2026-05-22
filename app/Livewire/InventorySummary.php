<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Organization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Envanter Özeti')]
class InventorySummary extends Component
{
    #[Url(as: 'org', except: null)]
    public ?int $organizationId = null;

    public function render()
    {
        $organizations = Organization::orderByDesc('event_date')->orderBy('name')->get();
        $organization = $this->organizationId ? Organization::find($this->organizationId) : null;

        $order = null;
        $inventory = [];
        $spongeCount = 0;
        $tableCount = 0;
        $bouquetCount = 0;
        $grandTotal = 0.0;
        $pricedCount = 0;
        $unpricedCount = 0;

        if ($organization) {
            $order = Order::where('organization_id', $organization->id)
                ->with([
                    'tables.tableType.flowerLines.flowerType',
                    'tables.extras.flowerType',
                    'bouquets.bouquetType.flowerLines.flowerType',
                    'looseFlowers.flowerType',
                ])
                ->first();
            if ($order) {
                $inventory = $order->aggregateFlowerInventory();
                $grandTotal = $order->grandTotal($inventory);
                foreach ($inventory as $row) {
                    $row['subtotal'] !== null ? $pricedCount++ : $unpricedCount++;
                }
                foreach ($order->tables as $ot) {
                    $tableCount  += $ot->table_count;
                    $spongeCount += ($ot->tableType?->sponge_count ?? 0) * $ot->table_count;
                }
                foreach ($order->bouquets as $ob) {
                    $bouquetCount += $ob->bouquet_count;
                }
            }
        }

        return view('livewire.inventory-summary', [
            'organizations' => $organizations,
            'organization'  => $organization,
            'order'         => $order,
            'inventory'     => $inventory,
            'spongeCount'   => $spongeCount,
            'tableCount'    => $tableCount,
            'bouquetCount'  => $bouquetCount,
            'grandTotal'    => $grandTotal,
            'pricedCount'   => $pricedCount,
            'unpricedCount' => $unpricedCount,
        ]);
    }
}
