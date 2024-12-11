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

        Schema::create('seller_product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_product_id')->constrained();
            $table->foreignId('seller_variant_id')->nullable()->constrained();
            $table->string('image');
            $table->integer('number')->default(1);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_product_images');
    }
};
