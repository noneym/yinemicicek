<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_type_flowers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flower_type_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_per_table', 10, 2);
            $table->timestamps();

            $table->unique(['table_type_id', 'flower_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_type_flowers');
    }
};
