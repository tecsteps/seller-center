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

        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('status', ["open","submitted","accepted","rejected","review"])->default('open');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('email')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->text('description')->nullable();
            $table->string('company_name')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('vat')->nullable();
            $table->string('tin')->nullable();
            $table->string('eori')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift_bic')->nullable();
            $table->string('bank_name')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
