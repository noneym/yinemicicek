<?php

namespace App\Livewire;

use App\Models\FlowerType;
use App\Models\TableType;
use App\Models\TableTypeFlower;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Masa Tipleri')]
class TableTypeManager extends Component
{
    use WithFileUploads;

    public ?int $editingId = null;
    public string $name = '';
    public ?string $description = null;
    public int $sponge_count = 1;
    public $image;

    /** @var array<int, array{flower_type_id: ?int, quantity_per_table: ?float}> */
    public array $lines = [];

    protected function rules(): array
    {
        return [
            'name'         => 'required|string|max:120',
            'description'  => 'nullable|string|max:2000',
            'sponge_count' => 'required|integer|min:0|max:1000',
            'image'        => 'nullable|image|max:4096',
            'lines.*.flower_type_id'      => 'required|integer|exists:flower_types,id',
            'lines.*.quantity_per_table'  => 'required|numeric|min:0.01',
        ];
    }

    public function mount(): void
    {
        $this->lines = [['flower_type_id' => null, 'quantity_per_table' => null]];
    }

    public function addLine(): void
    {
        $this->lines[] = ['flower_type_id' => null, 'quantity_per_table' => null];
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
        // Filter out completely empty lines
        $this->lines = array_values(array_filter(
            $this->lines,
            fn ($l) => !empty($l['flower_type_id']) && (float) ($l['quantity_per_table'] ?? 0) > 0
        ));
        if (empty($this->lines)) {
            $this->addError('lines', 'En az bir çiçek satırı girin.');
            return;
        }

        $data = $this->validate();

        DB::transaction(function () use ($data) {
            $table = TableType::find($this->editingId) ?? new TableType();
            $table->name = $data['name'];
            $table->description = $data['description'] ?: null;
            $table->sponge_count = $data['sponge_count'];

            if ($this->image) {
                if ($table->image_path) {
                    Storage::disk('public')->delete($table->image_path);
                }
                $table->image_path = $this->image->store('tables', 'public');
            }
            $table->save();

            $table->flowerLines()->delete();
            foreach ($this->lines as $line) {
                TableTypeFlower::create([
                    'table_type_id' => $table->id,
                    'flower_type_id' => $line['flower_type_id'],
                    'quantity_per_table' => $line['quantity_per_table'],
                ]);
            }
        });

        $this->resetForm();
        session()->flash('flash', 'Masa tipi kaydedildi.');
    }

    public function edit(int $id): void
    {
        $type = TableType::with('flowerLines')->findOrFail($id);
        $this->editingId    = $type->id;
        $this->name         = $type->name;
        $this->description  = $type->description;
        $this->sponge_count = $type->sponge_count;
        $this->image        = null;
        $this->lines = $type->flowerLines->map(fn ($l) => [
            'flower_type_id'      => $l->flower_type_id,
            'quantity_per_table'  => (float) $l->quantity_per_table,
        ])->all();
        if (empty($this->lines)) {
            $this->addLine();
        }
    }

    public function delete(int $id): void
    {
        $type = TableType::findOrFail($id);
        if ($type->image_path) {
            Storage::disk('public')->delete($type->image_path);
        }
        $type->delete();
        session()->flash('flash', 'Masa tipi silindi.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'description', 'image']);
        $this->sponge_count = 1;
        $this->lines = [['flower_type_id' => null, 'quantity_per_table' => null]];
    }

    public function render()
    {
        return view('livewire.table-type-manager', [
            'flowers'    => FlowerType::orderBy('name')->get(),
            'tableTypes' => TableType::with('flowerLines.flowerType')->orderBy('name')->get(),
        ]);
    }
}
