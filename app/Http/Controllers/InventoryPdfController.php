<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Organization;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryPdfController extends Controller
{
    public function __invoke(Organization $organization, Request $request)
    {
        $order = Order::where('organization_id', $organization->id)
            ->with([
                'tables.tableType.flowerLines.flowerType',
                'tables.extras.flowerType',
                'bouquets.bouquetType.flowerLines.flowerType',
                'looseFlowers.flowerType',
            ])
            ->first();

        abort_if(!$order, 404, 'Bu organizasyon için sipariş yok.');

        $inventory   = $order->aggregateFlowerInventory();
        $tableCount  = $order->tables->sum('table_count');
        $spongeCount = $order->tables->sum(fn ($ot) => ($ot->tableType?->sponge_count ?? 0) * $ot->table_count);
        $bouquetCount = $order->bouquets->sum('bouquet_count');

        $pdf = Pdf::loadView('pdf.inventory', [
            'organization' => $organization,
            'order'        => $order,
            'inventory'    => $inventory,
            'tableCount'   => $tableCount,
            'spongeCount'  => $spongeCount,
            'bouquetCount' => $bouquetCount,
            'generatedAt'  => now(),
        ])
        ->setPaper('a4', 'portrait')
        ->setOption('isRemoteEnabled', true)
        ->setOption('isHtml5ParserEnabled', true);

        $datePart = $organization->event_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $name = Str::slug($organization->name . '-' . ($organization->location ?? ''), '-');
        $filename = "yinemicicek-{$datePart}-{$name}.pdf";

        if ($request->boolean('html')) {
            // Tasarımı tarayıcıda kontrol için ham HTML
            return view('pdf.inventory', [
                'organization' => $organization,
                'order'        => $order,
                'inventory'    => $inventory,
                'tableCount'   => $tableCount,
                'spongeCount'  => $spongeCount,
                'bouquetCount' => $bouquetCount,
                'generatedAt'  => now(),
            ]);
        }

        return $request->boolean('preview')
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }
}
