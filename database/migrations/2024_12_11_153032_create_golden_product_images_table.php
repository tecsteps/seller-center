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

        Schema::create('golden_product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('golden_product_id')->constrained();
            $table->string('image');
            $table->integer('number')->default(1);
            $table->foreignId('seller_product_image_id')->constrained();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('golden_product_images');
    }
};
