<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name',50)->unique();
            $table->unsignedBigInteger('address_id')->unique();
            $table->enum('status', ['Enable', 'Disable', 'Block'])->default('Enable');
            $table->string('payment_gateway_name')->default('Stripe'); // Name of the payment gateway (e.g., Stripe, PayPal)
            $table->string('payment_gateway_merchant_id')->nullable(); // Merchant ID issued by the payment gateway
            $table->boolean('is_online_payment_enabled')->default(false);
            $table->unsignedBigInteger('owner_id')->unique();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('address_id')->references('id')->on('address');
            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
