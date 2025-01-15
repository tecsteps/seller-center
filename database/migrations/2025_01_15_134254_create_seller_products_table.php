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

        Schema::create('seller_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->json('attributes')->nullable();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('seller_id')->constrained();
            $table->enum('status', ["draft","active","delisted"])->default('draft');
            $table->boolean('selected')->default(false);
            $table->string('ean')->nullable();
            $table->string('upc')->nullable();
            $table->string('gtin_14')->nullable();
            $table->string('gtin_8')->nullable();
            $table->foreignId('golden_product_id')->nullable()->constrained();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_products');
    }
};
