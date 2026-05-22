<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">📝 Sipariş Girişi</h1>
                <p class="text-sm text-slate-600">Bir organizasyon seç ve içine masa / buket / serbest çiçek ekle. Toplam envanter otomatik hesaplanır.</p>
            </div>
            <div class="min-w-[260px]">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Organizasyon</label>
                <select wire:model.live="organizationId" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500">
                    <option value="">— Seç —</option>
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}">
                            {{ $org->event_date?->format('d.m.Y') ?? '—' }} · {{ $org->location ?? '—' }} ({{ $org->name }})
                        </option>
                    @endforeach
                </select>
                @if ($organizations->isEmpty())
                    <a href="{{ route('organizations') }}" class="text-xs text-brand-600 hover:underline mt-1 inline-block">+ Önce bir organizasyon ekle</a>
                @endif
            </div>
        </div>
    </div>

    @if ($order)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- MASALAR -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">🪑 Masalar</h2>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end mb-4 bg-slate-50 p-3 rounded-xl">
                        <div class="md:col-span-7">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Masa Tipi</label>
                            <select wire:model="newTableTypeId" class="w-full rounded-lg border-slate-300 text-sm">
                                <option value="">— Seç —</option>
                                @foreach ($tableTypes as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                            @error('newTableTypeId') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
                            @if ($tableTypes->isEmpty())
                                <a href="{{ route('tables') }}" class="text-xs text-brand-600 hover:underline">+ Önce masa tipi ekle</a>
                            @endif
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Masa Sayısı</label>
                            <input type="number" min="1" wire:model="newTableCount" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                        <div class="md:col-span-3">
                            <button wire:click="addTable" class="w-full bg-brand-600 hover:bg-brand-700 text-white rounded-lg px-3 py-2 text-sm font-medium">+ Masa Ekle</button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse ($order->tables as $ot)
                            <div class="border border-slate-200 rounded-xl">
                                <div class="flex items-center justify-between p-3 bg-slate-50 border-b border-slate-200 rounded-t-xl">
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $ot->tableType?->name ?? '⚠️ Silinmiş tip' }}</div>
                                        <div class="text-xs text-slate-500">{{ $ot->table_count }} masa × {{ $ot->tableType?->sponge_count ?? 0 }} sünger = <span class="font-mono font-medium">{{ $ot->table_count * ($ot->tableType?->sponge_count ?? 0) }} sünger</span></div>
                                    </div>
                                    <button wire:click="removeTable({{ $ot->id }})" wire:confirm="Bu masa satırını sil?" class="text-rose-600 hover:underline text-xs">🗑️ Sil</button>
                                </div>
                                <div class="p-3 space-y-1 text-sm">
                                    @foreach ($ot->tableType?->flowerLines ?? [] as $line)
                                        @php $total = $line->quantity_per_table * $ot->table_count; @endphp
                                        <div class="flex justify-between text-slate-700">
                                            <span>{{ $line->flowerType->name }}</span>
                                            <span class="font-mono">
                                                {{ rtrim(rtrim(number_format($line->quantity_per_table,2,',','.'),'0'),',') }} × {{ $ot->table_count }}
                                                = <strong>{{ rtrim(rtrim(number_format($total,2,',','.'),'0'),',') }}</strong> {{ $line->flowerType->unit }}
                                            </span>
                                        </div>
                                    @endforeach
                                    @foreach ($ot->extras as $extra)
                                        @php $total = $extra->quantity_per_table * $ot->table_count; @endphp
                                        <div class="flex justify-between text-emerald-700 bg-emerald-50 rounded px-2 py-1">
                                            <span>+ {{ $extra->flowerType?->name }} <span class="text-xs text-emerald-600">(ekstra)</span></span>
                                            <span class="font-mono flex items-center gap-2">
                                                {{ rtrim(rtrim(number_format($extra->quantity_per_table,2,',','.'),'0'),',') }} × {{ $ot->table_count }}
                                                = <strong>{{ rtrim(rtrim(number_format($total,2,',','.'),'0'),',') }}</strong> {{ $extra->flowerType?->unit }}
                                                <button wire:click="removeExtra({{ $extra->id }})" class="text-rose-500 hover:text-rose-700 text-xs ml-1">×</button>
                                            </span>
                                        </div>
                                    @endforeach
                                    <div class="pt-2 mt-2 border-t border-dashed border-slate-200 flex flex-wrap gap-2 items-end">
                                        <select wire:model="extraForm.{{ $ot->id }}.flower_type_id" class="rounded-lg border-slate-300 text-xs flex-1 min-w-[180px]">
                                            <option value="">+ Ekstra çiçek ekle…</option>
                                            @foreach ($flowers as $f)
                                                <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->unit }})</option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" min="0.01" wire:model="extraForm.{{ $ot->id }}.quantity_per_table" placeholder="adet/masa" class="rounded-lg border-slate-300 text-xs w-32">
                                        <button wire:click="addExtra({{ $ot->id }})" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg px-3 py-1.5 text-xs">+ Ekle</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-slate-400 py-6 border border-dashed border-slate-300 rounded-xl">Henüz masa eklenmemiş.</div>
                        @endforelse
                    </div>
                </div>

                <!-- BUKETLER -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">💐 Buketler</h2>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end mb-4 bg-slate-50 p-3 rounded-xl">
                        <div class="md:col-span-7">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Buket Tipi</label>
                            <select wire:model="newBouquetTypeId" class="w-full rounded-lg border-slate-300 text-sm">
                                <option value="">— Seç —</option>
                                @foreach ($bouquetTypes as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                            @error('newBouquetTypeId') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
                            @if ($bouquetTypes->isEmpty())
                                <a href="{{ route('bouquets') }}" class="text-xs text-brand-600 hover:underline">+ Önce buket tipi ekle</a>
                            @endif
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Adet</label>
                            <input type="number" min="1" wire:model="newBouquetCount" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                        <div class="md:col-span-3">
                            <button wire:click="addBouquet" class="w-full bg-brand-600 hover:bg-brand-700 text-white rounded-lg px-3 py-2 text-sm font-medium">+ Buket Ekle</button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse ($order->bouquets as $ob)
                            <div class="border border-slate-200 rounded-xl">
                                <div class="flex items-center justify-between p-3 bg-slate-50 border-b border-slate-200 rounded-t-xl">
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $ob->bouquetType?->name ?? '⚠️ Silinmiş tip' }}</div>
                                        <div class="text-xs text-slate-500">{{ $ob->bouquet_count }} buket</div>
                                    </div>
                                    <button wire:click="removeBouquet({{ $ob->id }})" wire:confirm="Sil?" class="text-rose-600 hover:underline text-xs">🗑️ Sil</button>
                                </div>
                                <div class="p-3 space-y-1 text-sm">
                                    @foreach ($ob->bouquetType?->flowerLines ?? [] as $line)
                                        @php $total = $line->quantity_per_bouquet * $ob->bouquet_count; @endphp
                                        <div class="flex justify-between text-slate-700">
                                            <span>{{ $line->flowerType->name }}</span>
                                            <span class="font-mono">
                                                {{ rtrim(rtrim(number_format($line->quantity_per_bouquet,2,',','.'),'0'),',') }} × {{ $ob->bouquet_count }}
                                                = <strong>{{ rtrim(rtrim(number_format($total,2,',','.'),'0'),',') }}</strong> {{ $line->flowerType->unit }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-slate-400 py-6 border border-dashed border-slate-300 rounded-xl">Henüz buket eklenmemiş.</div>
                        @endforelse
                    </div>
                </div>

                <!-- SERBEST ÇİÇEKLER -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">🌸 Serbest Çiçekler / Malzemeler</h2>
                    <p class="text-xs text-slate-500 mb-3">Masa veya bukete bağlı olmayan ek malzemeler (örn. "1 çuval yosun", "5 demet pembe lisianthus").</p>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end mb-4 bg-slate-50 p-3 rounded-xl">
                        <div class="md:col-span-5">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Çiçek</label>
                            <select wire:model="newLooseFlowerId" class="w-full rounded-lg border-slate-300 text-sm">
                                <option value="">— Seç —</option>
                                @foreach ($flowers as $f)
                                    <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->unit }})</option>
                                @endforeach
                            </select>
                            @error('newLooseFlowerId') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Miktar</label>
                            <input type="number" step="0.01" min="0.01" wire:model="newLooseQty" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Birim (ops.)</label>
                            <input type="text" wire:model="newLooseUnit" placeholder="örn. demet" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                        <div class="md:col-span-3">
                            <button wire:click="addLoose" class="w-full bg-brand-600 hover:bg-brand-700 text-white rounded-lg px-3 py-2 text-sm font-medium">+ Ekle</button>
                        </div>
                        <div class="md:col-span-12">
                            <input type="text" wire:model="newLooseNotes" placeholder="not (opsiyonel)" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                    </div>

                    <ul class="divide-y divide-slate-100">
                        @forelse ($order->looseFlowers as $lf)
                            <li class="py-2 flex items-center justify-between text-sm">
                                <div>
                                    <span class="font-medium text-slate-800">{{ $lf->flowerType?->name }}</span>
                                    <span class="font-mono text-slate-600 ml-2">
                                        {{ rtrim(rtrim(number_format($lf->quantity,2,',','.'),'0'),',') }} {{ $lf->unit_override ?: $lf->flowerType?->unit }}
                                    </span>
                                    @if ($lf->notes)
                                        <span class="text-xs text-slate-500 ml-2">— {{ $lf->notes }}</span>
                                    @endif
                                </div>
                                <button wire:click="removeLoose({{ $lf->id }})" wire:confirm="Sil?" class="text-rose-600 hover:underline text-xs">🗑️</button>
                            </li>
                        @empty
                            <li class="py-4 text-center text-slate-400">Serbest çiçek yok.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- NOTLAR -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-2">📝 Sipariş Notları</h2>
                    <textarea wire:model="orderNotes" rows="3" placeholder="Mood board, müşteri notları…" class="w-full rounded-lg border-slate-300 focus:border-brand-500 focus:ring-brand-500 text-sm"></textarea>
                    <button wire:click="saveOrderNotes" class="mt-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg px-4 py-2 text-sm">Notları Kaydet</button>
                </div>
            </div>

            <!-- ÖZET -->
            <aside class="lg:col-span-1">
                <div class="sticky top-4 space-y-4">
                    <div class="bg-gradient-to-br from-brand-50 to-pink-100 rounded-2xl border border-pink-200 p-5 shadow-sm">
                        <h3 class="text-base font-bold text-brand-800 mb-3 flex items-center gap-2">📊 Anlık Toplam</h3>
                        @php $inv = $order->aggregateFlowerInventory(); $gt = $order->grandTotal($inv); @endphp
                        @if (empty($inv))
                            <p class="text-sm text-brand-700/70">Henüz hesaplanacak bir şey yok. Masa, buket veya serbest çiçek ekle.</p>
                        @else
                            @if ($gt > 0)
                                <div class="bg-emerald-600 text-white rounded-lg p-3 mb-3 text-center">
                                    <div class="text-xs text-emerald-100">Toplam Maliyet</div>
                                    <div class="text-xl font-bold font-mono">{{ number_format($gt, 2, ',', '.') }} ₺</div>
                                </div>
                            @endif
                            <ul class="space-y-1.5 text-sm max-h-[55vh] overflow-y-auto pr-2">
                                @foreach ($inv as $item)
                                    <li class="flex items-center gap-2 bg-white/70 rounded-lg p-2">
                                        @if ($item['flower']->image_url)
                                            <img src="{{ $item['flower']->image_url }}" class="w-8 h-8 rounded object-cover border border-pink-100">
                                        @else
                                            <span class="w-8 h-8 rounded bg-pink-50 flex items-center justify-center text-base">🌸</span>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="truncate font-medium text-slate-800 text-xs">{{ $item['flower']->name }}</div>
                                            <div class="font-mono text-xs text-brand-700">
                                                @foreach ($item['totals'] as $unit => $qty)
                                                    {{ rtrim(rtrim(number_format($qty, 2, ',', '.'), '0'), ',') }} {{ $unit }}@if (!$loop->last) · @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @if ($item['subtotal'] !== null)
                                            <div class="font-mono text-xs font-semibold text-emerald-700 whitespace-nowrap">
                                                {{ number_format($item['subtotal'], 2, ',', '.') }} ₺
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('inventory', ['org' => $organizationId]) }}" class="block text-center text-xs text-brand-700 hover:underline mt-3">→ Detaylı envanter özeti</a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-dashed border-slate-300 p-12 text-center">
            <div class="text-4xl mb-3">🎉</div>
            <p class="text-slate-500">Devam etmek için yukarıdan bir organizasyon seç.</p>
        </div>
    @endif
</div>
