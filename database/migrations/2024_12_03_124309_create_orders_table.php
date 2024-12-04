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

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->enum('status', ["pending","processing","shipped","delivered","cancelled"])->default('pending');
            $table->integer('total_amount');
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('shipping_address');
            $table->text('billing_address');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
