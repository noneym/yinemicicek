@props([
    'open' => 'modalOpen',       // Livewire property name (string)
    'title' => '',
    'maxWidth' => 'max-w-3xl',
    'closeAction' => 'closeModal',
])

<div
    x-data="{ open: @entangle($open) }"
    x-show="open"
    x-cloak
    x-on:keydown.escape.window="$wire.{{ $closeAction }}()"
    class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-4 overflow-y-auto bg-slate-900/60 backdrop-blur-sm"
    wire:click.self="{{ $closeAction }}"
>
    <div
        wire:key="modal-content-{{ $open }}"
        @click.stop
        class="bg-white rounded-2xl {{ $maxWidth }} w-full shadow-2xl my-4 sm:my-8 max-h-[calc(100vh-2rem)] flex flex-col"
    >
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-white rounded-t-2xl">
            <h3 class="text-lg font-bold text-slate-900">{{ $title }}</h3>
            <button
                type="button"
                wire:click="{{ $closeAction }}"
                class="text-slate-400 hover:text-slate-700 text-3xl leading-none w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100"
                aria-label="Kapat"
            >&times;</button>
        </div>

        <div class="overflow-y-auto px-6 py-5 flex-1">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl flex flex-wrap justify-end gap-2">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
