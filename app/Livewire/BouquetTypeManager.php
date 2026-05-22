<?php

namespace App\Livewire;

use App\Models\BouquetFlower;
use App\Models\BouquetType;
use App\Models\FlowerType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Buket Tipleri')]
class BouquetTypeManager extends Component
{
    use WithFileUploads;

    public bool $modalOpen = false;
    public ?int $editingId = null;
    public string $name = '';
    public ?string $description = null;
    public $image;

    /** @var array<int, array{flower_type_id: ?int, quantity_per_bouquet: ?float}> */
    public array $lines = [];

    protected function rules(): array
    {
        return [
            'name'         => 'required|string|max:120',
            'description'  => 'nullable|string|max:2000',
            'image'        => 'nullable|image|max:4096',
            'lines.*.flower_type_id'        => 'required|integer|exists:flower_types,id',
            'lines.*.quantity_per_bouquet'  => 'required|numeric|min:0.01',
        ];
    }

    public function mount(): void
    {
        $this->lines = [['flower_type_id' => null, 'quantity_per_bouquet' => null]];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->modalOpen = true;
    }

    public function edit(int $id): void
    {
        $type = BouquetType::with('flowerLines')->findOrFail($id);
        $this->editingId    = $type->id;
        $this->name         = $type->name;
        $this->description  = $type->description;
        $this->image        = null;
        $this->lines = $type->flowerLines->map(fn ($l) => [
            'flower_type_id'        => $l->flower_type_id,
            'quantity_per_bouquet'  => (float) $l->quantity_per_bouquet,
        ])->all();
        if (empty($this->lines)) {
            $this->addLine();
        }
        $this->resetErrorBag();
        $this->modalOpen = true;
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->resetForm();
    }

    public function addLine(): void
    {
        $this->lines[] = ['flower_type_id' => null, 'quantity_per_bouquet' => null];
    }

    public function removeLine(int $index): void
    {
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
        if (empty($this->lines)) {
            $this->addLine();
        }
    }

    public function save(): void
    {
        $this->lines = array_values(array_filter(
            $this->lines,
            fn ($l) => !empty($l['flower_type_id']) && (float) ($l['quantity_per_bouquet'] ?? 0) > 0
        ));
        if (empty($this->lines)) {
            $this->addError('lines', 'En az bir çiçek satırı girin.');
            return;
        }

        $data = $this->validate();

        DB::transaction(function () use ($data) {
            $bouquet = BouquetType::find($this->editingId) ?? new BouquetType();
            $bouquet->name = $data['name'];
            $bouquet->description = $data['description'] ?: null;

            if ($this->image) {
                if ($bouquet->image_path) {
                    Storage::disk()->delete($bouquet->image_path);
                }
                $bouquet->image_path = $this->image->store('bouquets');
            }
            $bouquet->save();

            $bouquet->flowerLines()->delete();
            foreach ($this->lines as $line) {
                BouquetFlower::create([
                    'bouquet_type_id' => $bouquet->id,
                    'flower_type_id' => $line['flower_type_id'],
                    'quantity_per_bouquet' => $line['quantity_per_bouquet'],
                ]);
            }
        });

        $this->closeModal();
        session()->flash('flash', 'Buket tipi kaydedildi.');
    }

    public function delete(int $id): void
    {
        $type = BouquetType::findOrFail($id);
        if ($type->image_path) {
            Storage::disk()->delete($type->image_path);
        }
        $type->delete();
        session()->flash('flash', 'Buket tipi silindi.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'description', 'image']);
        $this->lines = [['flower_type_id' => null, 'quantity_per_bouquet' => null]];
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.bouquet-type-manager', [
            'flowers'  => FlowerType::orderBy('name')->get(),
            'bouquets' => BouquetType::with('flowerLines.flowerType')->orderBy('name')->get(),
        ]);
    }
}
