<?php

namespace App\Livewire;

use App\Models\BouquetType;
use App\Models\FlowerType;
use App\Models\Order;
use App\Models\OrderBouquet;
use App\Models\OrderLooseFlower;
use App\Models\OrderTable;
use App\Models\OrderTableExtra;
use App\Models\Organization;
use App\Models\TableType;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Sipariş Girişi')]
class OrderEntry extends Component
{
    #[Url(as: 'org', except: null)]
    public ?int $organizationId = null;

    public ?int $orderId = null;
    public ?string $orderNotes = null;

    /** Adding a table type */
    public ?int $newTableTypeId = null;
    public int $newTableCount = 1;

    /** Adding a bouquet */
    public ?int $newBouquetTypeId = null;
    public int $newBouquetCount = 1;

    /** Adding a loose flower */
    public ?int $newLooseFlowerId = null;
    public ?float $newLooseQty = null;
    public ?string $newLooseUnit = null;
    public ?string $newLooseNotes = null;

    /** Adding an extra to an existing order_table */
    public array $extraForm = []; // [order_table_id => ['flower_type_id' => ?, 'quantity_per_table' => ?]]

    public function mount(): void
    {
        if ($this->organizationId) {
            $this->loadOrderForOrganization();
        }
    }

    public function updatedOrganizationId(): void
    {
        $this->orderId = null;
        $this->loadOrderForOrganization();
    }

    protected function loadOrderForOrganization(): void
    {
        if (!$this->organizationId) {
            $this->orderId = null;
            $this->orderNotes = null;
            return;
        }
        $order = Order::firstOrCreate(['organization_id' => $this->organizationId]);
        $this->orderId = $order->id;
        $this->orderNotes = $order->notes;
    }

    public function saveOrderNotes(): void
    {
        if (!$this->orderId) return;
        Order::where('id', $this->orderId)->update(['notes' => $this->orderNotes]);
        session()->flash('flash', 'Notlar kaydedildi.');
    }

    public function addTable(): void
    {
        $this->validate([
            'newTableTypeId' => 'required|integer|exists:table_types,id',
            'newTableCount'  => 'required|integer|min:1|max:1000',
        ]);
        if (!$this->orderId) return;

        OrderTable::create([
            'order_id' => $this->orderId,
            'table_type_id' => $this->newTableTypeId,
            'table_count' => $this->newTableCount,
        ]);

        $this->reset(['newTableTypeId']);
        $this->newTableCount = 1;
    }

    public function removeTable(int $orderTableId): void
    {
        OrderTable::where('id', $orderTableId)
            ->where('order_id', $this->orderId)
            ->delete();
    }

    public function addBouquet(): void
    {
        $this->validate([
            'newBouquetTypeId' => 'required|integer|exists:bouquet_types,id',
            'newBouquetCount'  => 'required|integer|min:1|max:1000',
        ]);
        if (!$this->orderId) return;

        OrderBouquet::create([
            'order_id' => $this->orderId,
            'bouquet_type_id' => $this->newBouquetTypeId,
            'bouquet_count' => $this->newBouquetCount,
        ]);

        $this->reset(['newBouquetTypeId']);
        $this->newBouquetCount = 1;
    }

    public function removeBouquet(int $id): void
    {
        OrderBouquet::where('id', $id)->where('order_id', $this->orderId)->delete();
    }

    public function addLoose(): void
    {
        $this->validate([
            'newLooseFlowerId' => 'required|integer|exists:flower_types,id',
            'newLooseQty'      => 'required|numeric|min:0.01',
            'newLooseUnit'     => 'nullable|string|max:30',
            'newLooseNotes'    => 'nullable|string|max:500',
        ]);
        if (!$this->orderId) return;

        OrderLooseFlower::create([
            'order_id' => $this->orderId,
            'flower_type_id' => $this->newLooseFlowerId,
            'quantity' => $this->newLooseQty,
            'unit_override' => $this->newLooseUnit ?: null,
            'notes' => $this->newLooseNotes ?: null,
        ]);

        $this->reset(['newLooseFlowerId', 'newLooseQty', 'newLooseUnit', 'newLooseNotes']);
    }

    public function removeLoose(int $id): void
    {
        OrderLooseFlower::where('id', $id)->where('order_id', $this->orderId)->delete();
    }

    public function addExtra(int $orderTableId): void
    {
        $form = $this->extraForm[$orderTableId] ?? null;
        if (!$form || empty($form['flower_type_id']) || (float) ($form['quantity_per_table'] ?? 0) <= 0) {
            $this->addError('extraForm', 'Çiçek ve miktar girin.');
            return;
        }

        OrderTableExtra::create([
            'order_table_id' => $orderTableId,
            'flower_type_id' => $form['flower_type_id'],
            'quantity_per_table' => $form['quantity_per_table'],
        ]);

        unset($this->extraForm[$orderTableId]);
    }

    public function removeExtra(int $id): void
    {
        OrderTableExtra::find($id)?->delete();
    }

    public function render()
    {
        $organizations = Organization::orderByDesc('event_date')->orderBy('name')->get();
        $tableTypes    = TableType::orderBy('name')->get();
        $bouquetTypes  = BouquetType::orderBy('name')->get();
        $flowers       = FlowerType::orderBy('name')->get();

        $order = null;
        if ($this->orderId) {
            $order = Order::with([
                'organization',
                'tables.tableType.flowerLines.flowerType',
                'tables.extras.flowerType',
                'bouquets.bouquetType.flowerLines.flowerType',
                'looseFlowers.flowerType',
            ])->find($this->orderId);
        }

        return view('livewire.order-entry', [
            'organizations' => $organizations,
            'tableTypes'    => $tableTypes,
            'bouquetTypes'  => $bouquetTypes,
            'flowers'       => $flowers,
            'order'         => $order,
        ]);
    }
}
