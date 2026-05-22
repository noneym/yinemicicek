<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Çiçek Envanteri — {{ $organization->name }}</title>
    <style>
        * { box-sizing: border-box; }
        @page {
            margin: 0;
            size: A4 portrait;
        }
        body {
            margin: 0;
            font-family: "DejaVu Sans", sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
        }

        /* Decorative top stripe */
        .ribbon { height: 8px; background: #db2777; }
        .ribbon-table { width: 100%; height: 8px; border-collapse: collapse; }
        .ribbon-table td { padding: 0; }

        /* Header */
        .header {
            padding: 32px 40px 22px 40px;
            border-bottom: 1px solid #fbcfe8;
        }
        .header-table { width: 100%; }
        .header-table td { vertical-align: middle; }
        .brand-mark {
            color: #db2777;
            font-size: 32px;
            line-height: 1;
            margin-right: 8px;
            display: inline-block;
            vertical-align: middle;
        }
        .brand-name {
            display: inline-block;
            vertical-align: middle;
        }
        .brand {
            font-size: 24px;
            font-weight: 800;
            color: #be185d;
            letter-spacing: -0.5px;
            line-height: 1;
        }
        .brand-sub {
            font-size: 9px;
            color: #db2777;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 4px;
            font-weight: 600;
        }
        .header-meta {
            text-align: right;
            font-size: 9px;
            color: #94a3b8;
        }
        .header-meta strong { color: #475569; }

        /* Org card */
        .org-card {
            margin: 22px 40px 0 40px;
            padding: 20px 24px;
            border-radius: 14px;
            background: #fdf2f8;
            border: 1px solid #fbcfe8;
        }
        .org-table { width: 100%; }
        .org-title {
            font-size: 20px;
            font-weight: 700;
            color: #831843;
            margin: 0 0 6px 0;
            letter-spacing: -0.3px;
        }
        .org-sub {
            font-size: 11px;
            color: #be185d;
            letter-spacing: 0.3px;
        }
        .org-sub .sep {
            color: #f9a8d4;
            margin: 0 8px;
        }
        .org-sub .label {
            color: #db2777;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-right: 4px;
        }
        .org-stats {
            text-align: right;
            font-size: 9px;
            color: #be185d;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .org-stats .num {
            font-size: 22px;
            font-weight: 800;
            color: #831843;
            font-family: "DejaVu Sans", sans-serif;
            line-height: 1;
            margin-bottom: 3px;
        }

        /* Section header */
        .section-title {
            margin: 32px 40px 14px 40px;
            font-size: 11px;
            font-weight: 700;
            color: #831843;
            text-transform: uppercase;
            letter-spacing: 4px;
            padding-bottom: 8px;
            border-bottom: 2px solid #fbcfe8;
            position: relative;
        }
        .section-title .ornament {
            color: #db2777;
            margin-right: 8px;
            font-size: 14px;
        }

        /* Flower grid */
        .grid {
            margin: 0 36px;
            width: calc(100% - 72px);
            border-collapse: separate;
            border-spacing: 5px 5px;
        }
        .flower {
            width: 50%;
            background: #ffffff;
            border: 1px solid #fce7f3;
            border-radius: 10px;
            padding: 12px 14px;
            vertical-align: top;
        }
        .flower-row { width: 100%; }
        .flower-img {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            border: 1px solid #fbcfe8;
            object-fit: cover;
        }
        .flower-img-cell {
            width: 74px;
            vertical-align: top;
            padding-right: 12px;
        }
        .flower-placeholder {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            background: #fce7f3;
            color: #ec4899;
            text-align: center;
            line-height: 64px;
            font-size: 30px;
            border: 1px solid #fbcfe8;
        }
        .flower-info-cell { vertical-align: top; }
        .flower-name {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 2px 0;
        }
        .flower-color {
            font-size: 9px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .flower-qty {
            font-size: 18px;
            font-weight: 700;
            color: #db2777;
            line-height: 1;
        }
        .flower-unit {
            font-size: 10px;
            color: #94a3b8;
            font-weight: normal;
            margin-left: 2px;
        }

        /* Notes box */
        .notes {
            margin: 22px 40px 0 40px;
            padding: 14px 18px;
            border-left: 3px solid #f59e0b;
            background: #fffbeb;
            color: #78350f;
            font-size: 10px;
            border-radius: 0 8px 8px 0;
        }
        .notes-title {
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #b45309;
            margin-bottom: 6px;
        }

        /* Footer (normal flow — dompdf'in fixed+bg sorununu önler) */
        .footer-wrap {
            margin-top: 36px;
        }
        .footer {
            background-color: #be185d;
            color: #ffffff;
            font-size: 10px;
        }
        .footer-table { width: 100%; background-color: #be185d; }
        .footer-table td {
            vertical-align: middle;
            color: #ffffff;
            padding: 18px 40px;
            background-color: #be185d;
        }
        .footer-brand {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
        }
        .footer-brand .mark {
            color: #fbcfe8;
            margin-right: 6px;
        }
        .footer-tagline {
            font-size: 9px;
            color: #fbcfe8;
            margin-top: 3px;
            letter-spacing: 0.5px;
        }
        .footer-url {
            text-align: right;
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.3px;
        }
        .footer-url-sub {
            font-size: 9px;
            color: #fbcfe8;
            margin-top: 3px;
            font-style: italic;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

<div class="page-body">

    <table class="ribbon-table">
        <tr>
            <td style="background:#831843; width:20%;">&nbsp;</td>
            <td style="background:#be185d; width:30%;">&nbsp;</td>
            <td style="background:#db2777; width:25%;">&nbsp;</td>
            <td style="background:#f472b6; width:15%;">&nbsp;</td>
            <td style="background:#fbcfe8; width:10%;">&nbsp;</td>
        </tr>
    </table>

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <span class="brand-mark">❀</span>
                    <span class="brand-name">
                        <div class="brand">YineMiÇiçek</div>
                        <div class="brand-sub">Sipariş Envanter Listesi</div>
                    </span>
                </td>
                <td class="header-meta">
                    <div>Hazırlandı: <strong>{{ $generatedAt->format('d.m.Y H:i') }}</strong></div>
                    <div>Belge No: <strong>{{ strtoupper(substr(md5($order->id.$generatedAt->timestamp), 0, 8)) }}</strong></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="org-card">
        <table class="org-table">
            <tr>
                <td>
                    <div class="org-title">{{ $organization->name }}</div>
                    <div class="org-sub">
                        @if ($organization->event_date)<span class="label">Tarih</span>{{ $organization->event_date->format('d.m.Y') }}@endif
                        @if ($organization->event_date && $organization->location)<span class="sep">•</span>@endif
                        @if ($organization->location)<span class="label">Konum</span>{{ $organization->location }}@endif
                    </div>
                </td>
                <td class="org-stats">
                    <table style="margin-left: auto;">
                        <tr>
                            <td style="padding: 0 14px; text-align: center;">
                                <div class="num">{{ $tableCount }}</div>
                                <div>masa</div>
                            </td>
                            <td style="padding: 0 14px; text-align: center;">
                                <div class="num">{{ $spongeCount }}</div>
                                <div>sünger</div>
                            </td>
                            <td style="padding: 0 14px; text-align: center;">
                                <div class="num">{{ $bouquetCount }}</div>
                                <div>buket</div>
                            </td>
                            <td style="padding: 0 14px; text-align: center;">
                                <div class="num">{{ count($inventory) }}</div>
                                <div>çiçek</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">
        <span class="ornament">✿</span> Çiçek Listesi
    </div>

    @if (empty($inventory))
        <div style="text-align: center; color: #94a3b8; padding: 40px;">Henüz çiçek eklenmemiş.</div>
    @else
        <table class="grid">
            @php $items = array_values($inventory); $count = count($items); @endphp
            @for ($i = 0; $i < $count; $i += 2)
                <tr>
                    @for ($j = 0; $j < 2; $j++)
                        @php $item = $items[$i + $j] ?? null; @endphp
                        @if ($item)
                            <td class="flower">
                                <table class="flower-row">
                                    <tr>
                                        <td class="flower-img-cell">
                                            @if ($item['flower']->image_path)
                                                <img class="flower-img" src="{{ $item['flower']->image_url }}" alt="">
                                            @else
                                                <div class="flower-placeholder">❀</div>
                                            @endif
                                        </td>
                                        <td class="flower-info-cell">
                                            <div class="flower-name">{{ $item['flower']->name }}</div>
                                            @if ($item['flower']->color)
                                                <div class="flower-color">{{ $item['flower']->color }}</div>
                                            @endif
                                            @foreach ($item['totals'] as $unit => $qty)
                                                <div class="flower-qty">
                                                    {{ rtrim(rtrim(number_format($qty, 2, ',', '.'), '0'), ',') }}<span class="flower-unit">{{ $unit }}</span>
                                                </div>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        @else
                            <td style="width: 50%; border: none;"></td>
                        @endif
                    @endfor
                </tr>
            @endfor
        </table>
    @endif

    @if ($order->notes)
        <div class="notes">
            <div class="notes-title">Notlar</div>
            <div>{!! nl2br(e($order->notes)) !!}</div>
        </div>
    @endif

</div>

<div class="footer-wrap">
    <table class="footer-table">
        <tr>
            <td>
                <div class="footer-brand"><span class="mark">❀</span> YineMiÇiçek</div>
                <div class="footer-tagline">Çiçek Sipariş & Envanter Hesaplayıcı</div>
            </td>
            <td class="footer-url">
                yinemicicek.com
                <div class="footer-url-sub">Sen tasarla, biz sayalım.</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
