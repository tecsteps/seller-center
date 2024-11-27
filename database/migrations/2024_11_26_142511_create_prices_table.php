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

        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->foreignId('seller_variant_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('seller_product_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
