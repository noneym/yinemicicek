<?php

namespace App\Livewire;

use App\Models\Organization;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Organizasyonlar')]
class OrganizationManager extends Component
{
    public ?int $editingId = null;
    public string $name = '';
    public string $location = '';
    public ?string $event_date = null;
    public ?string $notes = null;

    protected function rules(): array
    {
        return [
            'name'       => 'required|string|max:120',
            'location'   => 'nullable|string|max:120',
            'event_date' => 'nullable|date',
            'notes'      => 'nullable|string|max:2000',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        Organization::updateOrCreate(
            ['id' => $this->editingId],
            $data
        );

        $this->resetForm();
        session()->flash('flash', 'Organizasyon kaydedildi.');
    }

    public function edit(int $id): void
    {
        $org = Organization::findOrFail($id);
        $this->editingId  = $org->id;
        $this->name       = $org->name;
        $this->location   = $org->location ?? '';
        $this->event_date = optional($org->event_date)->format('Y-m-d');
        $this->notes      = $org->notes;
    }

    public function delete(int $id): void
    {
        Organization::findOrFail($id)->delete();
        session()->flash('flash', 'Organizasyon silindi.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'location', 'event_date', 'notes']);
    }

    public function render()
    {
        return view('livewire.organization-manager', [
            'organizations' => Organization::orderByDesc('event_date')->orderBy('name')->get(),
        ]);
    }
}
