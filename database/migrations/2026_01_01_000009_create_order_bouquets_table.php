<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_bouquets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bouquet_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('bouquet_count');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_bouquets');
    }
};
