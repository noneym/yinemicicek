<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🌸</text></svg>">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#fdf2f8',
                            100: '#fce7f3',
                            200: '#fbcfe8',
                            300: '#f9a8d4',
                            400: '#f472b6',
                            500: '#ec4899',
                            600: '#db2777',
                            700: '#be185d',
                            800: '#9d174d',
                            900: '#831843',
                        },
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
    @livewireStyles
</head>
<body class="min-h-full text-slate-800 antialiased">
    <div class="min-h-screen flex flex-col">
        <header class="bg-gradient-to-r from-brand-600 via-brand-500 to-pink-400 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-between py-4 gap-3">
                    <a href="{{ route('orders') }}" class="flex items-center gap-3 text-white">
                        <span class="text-3xl">🌸</span>
                        <div>
                            <div class="text-xl font-extrabold tracking-tight">YineMiÇiçek</div>
                            <div class="text-xs text-pink-100 opacity-90">Sipariş & Envanter Hesaplayıcı</div>
                        </div>
                    </a>
                    <nav class="flex flex-wrap items-center gap-1 text-sm font-medium">
                        @php
                            $links = [
                                ['orders',        'Sipariş Girişi',    '📝'],
                                ['inventory',     'Envanter Özeti',    '📦'],
                                ['organizations', 'Organizasyonlar',   '🎉'],
                                ['flowers',       'Çiçek Tipleri',     '🌷'],
                                ['tables',        'Masa Tipleri',      '🪑'],
                                ['bouquets',      'Buket Tipleri',     '💐'],
                            ];
                            $current = request()->route()->getName();
                        @endphp
                        @foreach ($links as [$name, $label, $icon])
                            <a href="{{ route($name) }}"
                               class="px-3 py-2 rounded-lg transition flex items-center gap-1.5 {{ $current === $name ? 'bg-white text-brand-700 shadow' : 'text-white/90 hover:bg-white/20' }}">
                                <span>{{ $icon }}</span>
                                <span>{{ $label }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>
        </header>

        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div x-data="{ flash: '{{ session('flash') }}' }"
                 x-init="$watch('$wire.entangle', () => {})"
                 class="mb-4">
                @if (session('flash'))
                    <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                        {{ session('flash') }}
                    </div>
                @endif
            </div>

            {{ $slot }}
        </main>

        <footer class="bg-white border-t border-slate-200 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
                YineMiÇiçek &copy; {{ date('Y') }} &middot;
                <a href="https://yinemicicek.com" class="hover:text-brand-600">yinemicicek.com</a>
            </div>
        </footer>
    </div>

    @livewireScripts {{-- Livewire 3 already bundles Alpine.js; do NOT load it again --}}
</body>
</html>
