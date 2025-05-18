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
            $table->string('email',255)->unique();
            $table->string('phone',20)->unique();
            $table->string('logo',255)->nullable();

            $table->enum('status', ['Enable', 'Disable', 'Blocked'])->default('Enable');
            $table->string('payment_gateway_name')->default('Stripe'); // Name of the payment gateway (e.g., Stripe, PayPal)
            $table->string('payment_gateway_merchant_id')->nullable(); // Merchant ID issued by the payment gateway
            $table->boolean('is_online_payment_enabled')->default(false);

            $table->foreignId('owner_id')->unique()->constrained('users')->onDelete('cascade');
            $table->foreignId('address_id')->unique()->constrained('address')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
