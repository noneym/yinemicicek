<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">📦 Envanter Özeti</h1>
                <p class="text-sm text-slate-600">Bir organizasyon için ihtiyacın olan toplam çiçek listesi.</p>
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
            </div>
        </div>
    </div>

    @if ($organization && $order)
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                <div class="text-xs text-slate-500">Masa</div>
                <div class="text-2xl font-bold text-slate-900">{{ $tableCount }}</div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                <div class="text-xs text-slate-500">Sünger</div>
                <div class="text-2xl font-bold text-slate-900">{{ $spongeCount }}</div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                <div class="text-xs text-slate-500">Buket</div>
                <div class="text-2xl font-bold text-slate-900">{{ $bouquetCount }}</div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                <div class="text-xs text-slate-500">Çiçek Çeşidi</div>
                <div class="text-2xl font-bold text-slate-900">{{ count($inventory) }}</div>
            </div>
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl border border-emerald-700 p-4 shadow-sm text-white">
                <div class="text-xs text-emerald-100">Toplam Maliyet</div>
                <div class="text-2xl font-bold font-mono">{{ number_format($grandTotal, 2, ',', '.') }} ₺</div>
                @if ($unpricedCount > 0)
                    <div class="text-xs text-emerald-100 mt-0.5">⚠️ {{ $unpricedCount }} çiçeğin fiyatı yok</div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <h2 class="text-lg font-bold text-slate-900">🌸 Toplam Çiçek İhtiyacı</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('inventory.pdf', ['organization' => $organization->id]) }}"
                       class="text-xs bg-rose-600 hover:bg-rose-700 text-white rounded-lg px-3 py-1.5 inline-flex items-center gap-1 shadow-sm">
                        📄 PDF İndir <span class="text-rose-200 text-[10px]">(müşteriye)</span>
                    </a>
                    <button onclick="window.print()" class="text-xs bg-slate-700 hover:bg-slate-800 text-white rounded-lg px-3 py-1.5">🖨️ Yazdır</button>
                </div>
            </div>
            @if (empty($inventory))
                <div class="text-center text-slate-400 py-8">Hesaplanacak bir şey yok.</div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach ($inventory as $item)
                        <div class="border border-slate-200 rounded-xl overflow-hidden bg-gradient-to-br from-white to-pink-50/30 flex flex-col">
                            <div class="aspect-square bg-gradient-to-br from-pink-50 to-rose-100 flex items-center justify-center">
                                @if ($item['flower']->image_url)
                                    <img src="{{ $item['flower']->image_url }}" alt="{{ $item['flower']->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-5xl opacity-40">🌸</span>
                                @endif
                            </div>
                            <div class="p-3 flex-1">
                                <div class="font-semibold text-sm text-slate-900">{{ $item['flower']->name }}</div>
                                @if ($item['flower']->color)
                                    <div class="text-xs text-slate-500">{{ $item['flower']->color }}</div>
                                @endif
                                <div class="mt-2 space-y-0.5">
                                    @foreach ($item['totals'] as $unit => $qty)
                                        <div class="font-mono text-base font-bold text-brand-700">
                                            {{ rtrim(rtrim(number_format($qty, 2, ',', '.'), '0'), ',') }} <span class="text-xs text-slate-500">{{ $unit }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($item['subtotal'] !== null)
                                    <div class="mt-2 pt-2 border-t border-slate-100">
                                        <div class="font-mono text-sm font-bold text-emerald-700">{{ number_format($item['subtotal'], 2, ',', '.') }} ₺</div>
                                        <div class="text-[10px] text-slate-400">{{ number_format((float)$item['flower']->unit_price, 2, ',', '.') }} ₺ / {{ $item['flower']->unit }}</div>
                                    </div>
                                @elseif ($item['flower']->unit_price === null)
                                    <div class="mt-2 pt-2 border-t border-slate-100">
                                        <div class="text-[10px] text-slate-400 italic">fiyat tanımlı değil</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-4 border-t border-slate-200 text-sm">
                    <h3 class="font-semibold text-slate-700 mb-2">📋 Düz Liste (kopyalanabilir)</h3>
                    <textarea readonly rows="{{ min(24, count($inventory) + 4) }}" class="w-full font-mono text-xs bg-slate-50 rounded-lg border-slate-200 p-3" onclick="this.select()">@php
$lines = [];
$lines[] = $organization->name . ' — ' . ($organization->event_date?->format('d.m.Y') ?? '');
$lines[] = str_repeat('-', 50);
foreach ($inventory as $item) {
    $parts = [];
    foreach ($item['totals'] as $unit => $qty) {
        $parts[] = rtrim(rtrim(number_format($qty, 2, ',', '.'), '0'), ',') . ' ' . $unit;
    }
    $line = $item['flower']->name . ': ' . implode(' + ', $parts);
    if ($item['subtotal'] !== null) {
        $line .= '  → ' . number_format($item['subtotal'], 2, ',', '.') . ' ₺';
    }
    $lines[] = $line;
}
$lines[] = str_repeat('-', 50);
$lines[] = 'TOPLAM: ' . number_format($grandTotal, 2, ',', '.') . ' ₺';
if ($unpricedCount > 0) {
    $lines[] = '(Not: ' . $unpricedCount . ' çiçeğin fiyatı tanımlı değil, toplam dışında kaldı)';
}
echo implode("\n", $lines);
@endphp</textarea>
                </div>
            @endif
        </div>

        @if ($order->notes)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
                <h3 class="font-semibold text-amber-900 mb-1">📝 Sipariş Notları</h3>
                <div class="text-sm text-amber-800 whitespace-pre-line">{{ $order->notes }}</div>
            </div>
        @endif
    @elseif ($organizationId && !$order)
        <div class="bg-white rounded-2xl shadow-sm border border-dashed border-slate-300 p-12 text-center">
            <p class="text-slate-500 mb-3">Bu organizasyon için henüz sipariş yok.</p>
            <a href="{{ route('orders', ['org' => $organizationId]) }}" class="inline-block bg-brand-600 hover:bg-brand-700 text-white rounded-lg px-4 py-2 text-sm">+ Sipariş Girişine Geç</a>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-dashed border-slate-300 p-12 text-center">
            <div class="text-4xl mb-3">📦</div>
            <p class="text-slate-500">Yukarıdan bir organizasyon seç.</p>
        </div>
    @endif

    <style>
        @media print {
            nav, header, button, footer, aside, textarea { display: none !important; }
            body { background: white; }
        }
    </style>
</div>
