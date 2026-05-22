<div class="space-y-4">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">🌷 Çiçek Tipleri</h1>
            <p class="text-xs text-slate-500 mt-0.5">Renk, birim, görsel & not.</p>
        </div>
        <div class="flex items-center gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Çiçek ara…" class="rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm">
            <button wire:click="openCreate" class="bg-brand-600 hover:bg-brand-700 text-white rounded-lg px-4 py-2 font-medium shadow-sm text-sm whitespace-nowrap">
                + Yeni Çiçek
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <div class="text-xs text-slate-500 mb-3">{{ $flowers->count() }} çiçek</div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @forelse ($flowers as $flower)
                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white hover:shadow-md transition group">
                    <div class="relative aspect-square bg-gradient-to-br from-pink-50 to-rose-100 flex items-center justify-center">
                        @if ($flower->image_url)
                            <img src="{{ $flower->image_url }}" alt="{{ $flower->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl opacity-40">🌸</span>
                        @endif
                    </div>
                    <div class="p-2">
                        <div class="font-semibold text-sm text-slate-800 truncate" title="{{ $flower->name }}">{{ $flower->name }}</div>
                        <div class="text-xs text-slate-500">{{ $flower->color ?? '—' }} · {{ $flower->unit }}</div>
                        @if ($flower->unit_price !== null)
                            <div class="text-xs font-mono font-semibold text-emerald-700 mt-0.5">
                                {{ number_format((float)$flower->unit_price, 2, ',', '.') }} ₺ <span class="text-slate-400 font-normal">/ {{ $flower->unit }}</span>
                            </div>
                        @else
                            <div class="text-xs text-slate-400 italic mt-0.5">fiyat yok</div>
                        @endif
                        <div class="flex justify-between mt-2">
                            <button wire:click="edit({{ $flower->id }})" class="text-brand-600 hover:underline text-xs font-medium">Düzenle</button>
                            <button wire:click="delete({{ $flower->id }})" wire:confirm="Silmek istediğine emin misin?" class="text-rose-600 hover:underline text-xs">Sil</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-slate-400 py-8">Çiçek bulunamadı.</div>
            @endforelse
        </div>
    </div>

    <x-modal :title="$editingId ? 'Çiçek Düzenle' : 'Yeni Çiçek Ekle'" max-width="max-w-2xl">
        <form wire:submit="save" id="flower-form" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Ad *</label>
                    <input type="text" wire:model="name" autofocus class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="örn. Pembe Lisianthus">
                    @error('name') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Renk</label>
                    <input type="text" wire:model="color" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="örn. pembe">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Birim *</label>
                    <select wire:model.live="unit" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500">
                        <option value="adet">adet</option>
                        <option value="demet">demet</option>
                        <option value="dal">dal</option>
                        <option value="çuval">çuval</option>
                        <option value="kg">kg</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Birim Fiyat (₺ / {{ $unit ?: 'adet' }})</label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" wire:model="unit_price" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 pr-12" placeholder="örn. 12.50">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₺</span>
                    </div>
                    @error('unit_price') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Görsel</label>
                    <div class="flex items-start gap-3">
                        @if ($editingId && !$image)
                            @php $current = \App\Models\FlowerType::find($editingId); @endphp
                            @if ($current?->image_url)
                                <div class="relative">
                                    <img src="{{ $current->image_url }}" class="w-20 h-20 rounded-lg object-cover border border-slate-200">
                                    <button type="button" wire:click="removeImage({{ $editingId }})" wire:confirm="Görseli kaldır?" class="absolute -top-1 -right-1 bg-white rounded-full border border-slate-200 w-6 h-6 flex items-center justify-center text-xs hover:bg-rose-50">×</button>
                                </div>
                            @endif
                        @endif
                        <div class="flex-1">
                            <input type="file" wire:model="image" accept="image/*" class="w-full text-xs file:rounded-lg file:border-0 file:bg-brand-100 file:text-brand-800 file:px-3 file:py-2 file:mr-2 file:cursor-pointer">
                            <div wire:loading wire:target="image" class="text-xs text-slate-500 mt-1">Yükleniyor…</div>
                            @error('image') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
                            @if ($image)
                                <div class="mt-2 flex items-center gap-2">
                                    <img src="{{ $image->temporaryUrl() }}" class="h-20 w-20 object-cover rounded-lg border border-emerald-300">
                                    <div>
                                        <div class="text-xs font-semibold text-emerald-700">Yeni görsel hazır ✓</div>
                                        <button type="button" wire:click="$set('image', null)" class="text-xs text-rose-600 hover:underline mt-1">İptal et</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Notlar</label>
                    <textarea wire:model="notes" rows="2" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500"></textarea>
                </div>
            </div>
        </form>

        <x-slot:footer>
            <button type="button" wire:click="closeModal" class="rounded-lg border border-slate-300 px-4 py-2 hover:bg-slate-100 text-sm">İptal</button>
            <button type="submit" form="flower-form" class="bg-brand-600 hover:bg-brand-700 text-white rounded-lg px-5 py-2 font-medium shadow-sm text-sm">
                <span wire:loading.remove wire:target="save">{{ $editingId ? 'Güncelle' : 'Ekle' }}</span>
                <span wire:loading wire:target="save">Kaydediliyor…</span>
            </button>
        </x-slot:footer>
    </x-modal>
</div>
