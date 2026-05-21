<?php

use App\Livewire\BouquetTypeManager;
use App\Livewire\FlowerTypeManager;
use App\Livewire\InventorySummary;
use App\Livewire\OrderEntry;
use App\Livewire\OrganizationManager;
use App\Livewire\TableTypeManager;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/siparis');

Route::get('/organizasyonlar', OrganizationManager::class)->name('organizations');
Route::get('/siparis', OrderEntry::class)->name('orders');
Route::get('/envanter', InventorySummary::class)->name('inventory');
Route::get('/ayarlar/cicekler', FlowerTypeManager::class)->name('flowers');
Route::get('/ayarlar/masalar', TableTypeManager::class)->name('tables');
Route::get('/ayarlar/buketler', BouquetTypeManager::class)->name('bouquets');
