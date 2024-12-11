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

        Schema::create('product_type_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->enum('type', ["text","boolean","number","select","url","color"]);
            $table->boolean('is_translatable')->default(false);
            $table->enum('field', ["TextInput","Textarea","Checkbox","Toggle","Select","ColorPicker"]);
            $table->boolean('required')->default(false);
            $table->integer('rank')->default(0);
            $table->string('description')->nullable();
            $table->string('unit')->nullable();
            $table->boolean('is_variant_attribute')->default(false);
            $table->json('validators')->nullable();
            $table->foreignId('product_type_id')->constrained();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_type_attributes');
    }
};
