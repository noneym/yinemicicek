<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-slate-900">🎉 Organizasyonlar</h1>
        </div>

        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Organizasyon Adı *</label>
                <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="örn. Ayşe & Mehmet Düğünü">
                @error('name') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Konum</label>
                <input type="text" wire:model="location" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="örn. İzmir">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tarih</label>
                <input type="date" wire:model="event_date" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white rounded-lg px-4 py-2 font-medium shadow-sm">
                    {{ $editingId ? 'Güncelle' : '+ Ekle' }}
                </button>
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="rounded-lg border border-slate-300 px-3 py-2 hover:bg-slate-50">İptal</button>
                @endif
            </div>
            <div class="md:col-span-12">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Notlar</label>
                <textarea wire:model="notes" rows="2" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="opsiyonel"></textarea>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left">Tarih</th>
                    <th class="px-4 py-3 text-left">Organizasyon</th>
                    <th class="px-4 py-3 text-left">Konum</th>
                    <th class="px-4 py-3 text-left">Notlar</th>
                    <th class="px-4 py-3 text-right">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($organizations as $org)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 whitespace-nowrap font-mono text-slate-700">
                            {{ $org->event_date?->format('d.m.Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $org->name }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $org->location ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600 text-xs max-w-md truncate">{{ $org->notes }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('orders', ['org' => $org->id]) }}" class="text-emerald-700 hover:underline text-xs mr-3">Siparişe Geç</a>
                            <button wire:click="edit({{ $org->id }})" class="text-brand-600 hover:underline text-xs mr-3">Düzenle</button>
                            <button wire:click="delete({{ $org->id }})" wire:confirm="Bu organizasyonu silmek istediğine emin misin? Siparişler de silinecek." class="text-rose-600 hover:underline text-xs">Sil</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Henüz organizasyon eklenmemiş.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
