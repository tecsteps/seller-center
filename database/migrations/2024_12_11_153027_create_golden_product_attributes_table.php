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

        Schema::create('golden_product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_attribute_id')->constrained();
            $table->foreignId('golden_product_id')->constrained();
            $table->boolean('is_option')->default(false);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('golden_product_attributes');
    }
};
