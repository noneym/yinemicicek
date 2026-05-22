<?php

namespace App\Livewire;

use App\Models\FlowerType;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Çiçek Tipleri')]
class FlowerTypeManager extends Component
{
    use WithFileUploads;

    public bool $modalOpen = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $color = '';
    public string $unit = 'adet';
    public ?float $unit_price = null;
    public ?string $notes = null;

    #[Validate('nullable|image|max:4096')]
    public $image;

    public string $search = '';

    protected function rules(): array
    {
        return [
            'name'        => 'required|string|max:120',
            'color'       => 'nullable|string|max:60',
            'unit'        => 'required|string|max:30',
            'unit_price'  => 'nullable|numeric|min:0|max:9999999.99',
            'notes'       => 'nullable|string|max:2000',
            'image'       => 'nullable|image|max:4096',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->modalOpen = true;
    }

    public function edit(int $id): void
    {
        $flower = FlowerType::findOrFail($id);
        $this->editingId  = $flower->id;
        $this->name       = $flower->name;
        $this->color      = $flower->color ?? '';
        $this->unit       = $flower->unit ?? 'adet';
        $this->unit_price = $flower->unit_price !== null ? (float) $flower->unit_price : null;
        $this->notes      = $flower->notes;
        $this->image      = null;
        $this->resetErrorBag();
        $this->modalOpen  = true;
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $data = $this->validate();

        $flower = FlowerType::find($this->editingId) ?? new FlowerType();
        $flower->name       = $data['name'];
        $flower->color      = $data['color'] ?: null;
        $flower->unit       = $data['unit'];
        $flower->unit_price = $data['unit_price'] !== null && $data['unit_price'] !== '' ? $data['unit_price'] : null;
        $flower->notes      = $data['notes'] ?: null;

        if ($this->image) {
            if ($flower->image_path) {
                Storage::disk()->delete($flower->image_path);
            }
            $flower->image_path = $this->image->store('flowers');
        }

        $flower->save();

        $this->closeModal();
        session()->flash('flash', 'Çiçek kaydedildi.');
    }

    public function removeImage(int $id): void
    {
        $flower = FlowerType::findOrFail($id);
        if ($flower->image_path) {
            Storage::disk()->delete($flower->image_path);
            $flower->image_path = null;
            $flower->save();
        }
    }

    public function delete(int $id): void
    {
        $flower = FlowerType::findOrFail($id);
        if ($flower->image_path) {
            Storage::disk()->delete($flower->image_path);
        }
        $flower->delete();
        session()->flash('flash', 'Çiçek silindi.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'color', 'unit', 'unit_price', 'notes', 'image']);
        $this->unit = 'adet';
        $this->resetErrorBag();
    }

    public function render()
    {
        $q = FlowerType::query();
        if ($this->search !== '') {
            $q->where('name', 'like', '%'.$this->search.'%')
              ->orWhere('color', 'like', '%'.$this->search.'%');
        }
        return view('livewire.flower-type-manager', [
            'flowers' => $q->orderBy('name')->get(),
        ]);
    }
}
