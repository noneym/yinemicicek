@props([
    'options'     => [],          // [['value' => x, 'label' => 'Foo', 'image' => '?', 'meta' => '?'], ...]
    'placeholder' => 'Seç…',
    'searchPlaceholder' => '🔍 ara…',
    'emptyText'   => 'Sonuç bulunamadı',
    'size'        => 'sm',         // sm | md
])

@php
    // wire:model attribute → string adı (yoksa null)
    $wire = $attributes->wire('model');
    $wireModel = method_exists($wire, 'value') ? $wire->value() : null;
@endphp

<div
    x-data="searchableSelect({
        @if ($wireModel) selected: @entangle($wireModel), @endif
        options: @js(array_values($options)),
    })"
    @click.outside="close()"
    @keydown.escape.window="open && close()"
    class="relative"
    wire:ignore.self
>
    <button
        type="button"
        @click="toggle()"
        @keydown.arrow-down.prevent="open ? highlightNext() : open = true"
        @keydown.arrow-up.prevent="open ? highlightPrev() : open = true"
        @keydown.enter.prevent="open ? selectHighlighted() : open = true"
        class="w-full rounded-lg border-slate-300 px-3 {{ $size === 'sm' ? 'py-2 text-sm' : 'py-2.5' }} text-left bg-white hover:bg-slate-50 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 flex items-center justify-between gap-2"
    >
        <span class="flex items-center gap-2 min-w-0 flex-1">
            <template x-if="selectedOption?.image">
                <img :src="selectedOption.image" class="w-5 h-5 rounded object-cover flex-shrink-0">
            </template>
            <span class="truncate" :class="{ 'text-slate-400': !selectedOption }" x-text="selectedOption ? selectedOption.label : '{{ $placeholder }}'"></span>
        </span>
        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition.opacity.duration.100ms
        class="absolute z-40 mt-1 w-full min-w-[260px] bg-white rounded-lg shadow-xl border border-slate-200 overflow-hidden"
    >
        <div class="p-2 border-b border-slate-100 bg-slate-50">
            <input
                x-ref="search"
                x-model="search"
                @input="highlightIndex = 0"
                @keydown.arrow-down.prevent="highlightNext()"
                @keydown.arrow-up.prevent="highlightPrev()"
                @keydown.enter.prevent="selectHighlighted()"
                @keydown.escape.stop="close()"
                type="text"
                placeholder="{{ $searchPlaceholder }}"
                class="w-full rounded-md border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"
            >
        </div>
        <ul class="max-h-64 overflow-y-auto" x-ref="list">
            <template x-for="(opt, i) in filtered" :key="opt.value">
                <li
                    @click="select(opt.value)"
                    @mouseenter="highlightIndex = i"
                    :class="{
                        'bg-brand-50 text-brand-700 font-medium': String(selected) === String(opt.value),
                        'bg-slate-100': highlightIndex === i && String(selected) !== String(opt.value),
                    }"
                    class="px-3 py-2 text-sm cursor-pointer flex items-center gap-2"
                >
                    <template x-if="opt.image">
                        <img :src="opt.image" class="w-6 h-6 rounded object-cover flex-shrink-0">
                    </template>
                    <span class="flex-1 truncate" x-text="opt.label"></span>
                    <template x-if="opt.meta">
                        <span class="text-xs text-slate-400 flex-shrink-0" x-text="opt.meta"></span>
                    </template>
                </li>
            </template>
            <li x-show="filtered.length === 0" class="px-3 py-6 text-center text-xs text-slate-400">
                {{ $emptyText }}
            </li>
        </ul>
    </div>
</div>

{{-- Alpine.data('searchableSelect', ...) layout/app.blade.php içinde global kayıtlı --}}
