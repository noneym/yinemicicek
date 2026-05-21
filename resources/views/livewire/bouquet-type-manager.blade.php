<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h1 class="text-2xl font-bold text-slate-900 mb-4">💐 Buket Tipleri</h1>
        <p class="text-sm text-slate-600 mb-4">Bir bukete kaç çiçek girdiğini tanımla. Sipariş girerken buket sayısı ile çarpılır.</p>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-5">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Buket Adı *</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="örn. Romantik Pembe Buket">
                    @error('name') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="md:col-span-5">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Görsel</label>
                    <input type="file" wire:model="image" accept="image/*" class="w-full text-xs file:rounded-lg file:border-0 file:bg-brand-100 file:text-brand-800 file:px-3 file:py-2 file:mr-2 file:cursor-pointer">
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white rounded-lg px-4 py-2 font-medium shadow-sm">
                        {{ $editingId ? 'Güncelle' : '+ Kaydet' }}
                    </button>
                    @if ($editingId)
                        <button type="button" wire:click="resetForm" class="rounded-lg border border-slate-300 px-3 py-2 hover:bg-slate-50 text-xs">İptal</button>
                    @endif
                </div>
                <div class="md:col-span-12">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Açıklama</label>
                    <textarea wire:model="description" rows="1" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500"></textarea>
                </div>
            </div>

            <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-slate-700">Bir Bukette Kullanılacak Çiçekler</h3>
                    <button type="button" wire:click="addLine" class="text-brand-600 hover:text-brand-800 text-sm font-medium">+ Çiçek Satırı</button>
                </div>
                <div class="space-y-2">
                    @foreach ($lines as $i => $line)
                        <div class="grid grid-cols-12 gap-2 items-center bg-white p-2 rounded-lg border border-slate-200">
                            <div class="col-span-7">
                                <select wire:model="lines.{{ $i }}.flower_type_id" class="w-full rounded-lg border-slate-300 text-sm">
                                    <option value="">Çiçek seç…</option>
                                    @foreach ($flowers as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->unit }})</option>
                                    @endforeach
                                </select>
                                @error("lines.$i.flower_type_id") <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-span-3">
                                <input type="number" step="0.01" min="0.01" wire:model="lines.{{ $i }}.quantity_per_bouquet" class="w-full rounded-lg border-slate-300 text-sm" placeholder="adet/buket">
                                @error("lines.$i.quantity_per_bouquet") <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-span-2 text-right">
                                <button type="button" wire:click="removeLine({{ $i }})" class="text-rose-600 hover:text-rose-800 text-sm">🗑️ Sil</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('lines') <div class="text-rose-600 text-xs mt-2">{{ $message }}</div> @enderror
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($bouquets as $type)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                @if ($type->image_url)
                    <img src="{{ $type->image_url }}" class="w-full h-40 object-cover" alt="{{ $type->name }}">
                @endif
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div class="font-semibold text-slate-900">{{ $type->name }}</div>
                        <div class="flex gap-2 text-xs">
                            <button wire:click="edit({{ $type->id }})" class="text-brand-600 hover:underline">Düzenle</button>
                            <button wire:click="delete({{ $type->id }})" wire:confirm="Sil?" class="text-rose-600 hover:underline">Sil</button>
                        </div>
                    </div>
                    @if ($type->description)
                        <div class="text-xs text-slate-600 mt-1">{{ $type->description }}</div>
                    @endif
                    <ul class="text-sm text-slate-700 mt-3 space-y-1">
                        @foreach ($type->flowerLines as $line)
                            <li class="flex justify-between"><span>{{ $line->flowerType->name }}</span><span class="font-mono font-medium">{{ rtrim(rtrim(number_format($line->quantity_per_bouquet, 2, ',', '.'), '0'), ',') }} {{ $line->flowerType->unit }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @empty
            <div class="md:col-span-3 text-center text-slate-400 py-8">Henüz buket tipi yok.</div>
        @endforelse
    </div>
</div>
