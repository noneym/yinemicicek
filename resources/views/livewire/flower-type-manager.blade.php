<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-4">🌷 Çiçek Tipleri</h1>

        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Ad *</label>
                <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="örn. Pembe Lisianthus">
                @error('name') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Renk</label>
                <input type="text" wire:model="color" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="örn. pembe">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Birim *</label>
                <select wire:model="unit" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500">
                    <option value="adet">adet</option>
                    <option value="demet">demet</option>
                    <option value="dal">dal</option>
                    <option value="çuval">çuval</option>
                    <option value="kg">kg</option>
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Görsel</label>
                <input type="file" wire:model="image" accept="image/*" class="w-full text-xs file:rounded-lg file:border-0 file:bg-brand-100 file:text-brand-800 file:px-3 file:py-2 file:mr-2 file:cursor-pointer">
                <div wire:loading wire:target="image" class="text-xs text-slate-500 mt-1">Yükleniyor…</div>
                @error('image') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white rounded-lg px-4 py-2 font-medium shadow-sm">
                    {{ $editingId ? 'Güncelle' : '+ Ekle' }}
                </button>
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="rounded-lg border border-slate-300 px-3 py-2 hover:bg-slate-50 text-xs">İptal</button>
                @endif
            </div>
            <div class="md:col-span-12">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Notlar</label>
                <textarea wire:model="notes" rows="1" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500"></textarea>
            </div>

            @if ($image)
                <div class="md:col-span-12">
                    <span class="text-xs text-slate-600">Önizleme:</span>
                    <img src="{{ $image->temporaryUrl() }}" class="h-24 w-24 object-cover rounded-lg mt-1 border border-slate-200">
                </div>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <div class="flex items-center justify-between mb-3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Çiçek ara…" class="rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm">
            <span class="text-xs text-slate-500">{{ $flowers->count() }} çiçek</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @forelse ($flowers as $flower)
                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white hover:shadow-md transition group">
                    <div class="relative aspect-square bg-gradient-to-br from-pink-50 to-rose-100 flex items-center justify-center">
                        @if ($flower->image_url)
                            <img src="{{ $flower->image_url }}" alt="{{ $flower->name }}" class="w-full h-full object-cover">
                            <button wire:click="removeImage({{ $flower->id }})" wire:confirm="Görseli kaldır?" class="absolute top-1 right-1 bg-white/90 rounded-full p-1 text-xs opacity-0 group-hover:opacity-100 transition">❌</button>
                        @else
                            <span class="text-4xl opacity-40">🌸</span>
                        @endif
                    </div>
                    <div class="p-2">
                        <div class="font-semibold text-sm text-slate-800 truncate" title="{{ $flower->name }}">{{ $flower->name }}</div>
                        <div class="text-xs text-slate-500">{{ $flower->color ?? '—' }} · {{ $flower->unit }}</div>
                        <div class="flex justify-between mt-2">
                            <button wire:click="edit({{ $flower->id }})" class="text-brand-600 hover:underline text-xs">Düzenle</button>
                            <button wire:click="delete({{ $flower->id }})" wire:confirm="Silmek istediğine emin misin?" class="text-rose-600 hover:underline text-xs">Sil</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-slate-400 py-8">Çiçek bulunamadı.</div>
            @endforelse
        </div>
    </div>
</div>
