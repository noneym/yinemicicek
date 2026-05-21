<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('table_count');
            $table->timestamps();
        });

        Schema::create('order_table_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_table_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flower_type_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_per_table', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_table_extras');
        Schema::dropIfExists('order_tables');
    }
};
