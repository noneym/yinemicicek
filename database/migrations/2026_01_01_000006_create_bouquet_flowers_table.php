<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bouquet_flowers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bouquet_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flower_type_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_per_bouquet', 10, 2);
            $table->timestamps();

            $table->unique(['bouquet_type_id', 'flower_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bouquet_flowers');
    }
};
