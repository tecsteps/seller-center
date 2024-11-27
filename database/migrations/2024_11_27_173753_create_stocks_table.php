<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->integer('reserved')->nullable();
            $table->integer('safety_stock')->nullable();
            $table->foreignId('seller_variant_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('seller_product_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
