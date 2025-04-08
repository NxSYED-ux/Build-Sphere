<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planServicePrices', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('service_id');

            $table->string('billing_cycle');
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('PKR');

            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('planServices')->onDelete('cascade');
            $table->unique(['service_id', 'billing_cycle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planServicePrices');
    }
};
