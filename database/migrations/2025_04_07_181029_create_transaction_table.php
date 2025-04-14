<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_title');
            $table->string('transaction_category'); // New, Adjustment

            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->string('buyer_type')->nullable();
            $table->string('buyer_transaction_type')->default('Debit');

            $table->unsignedBigInteger('seller_id')->nullable();
            $table->string('seller_type')->nullable();
            $table->string('seller_transaction_type')->default('Credit');

            // Fast tracking references (optional)
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained('buildingunits')->onDelete('cascade');

            $table->string('payment_method')->default('Cash');
            $table->string('gateway_payment_id')->nullable();

            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('PKR');

            $table->enum('status', ['Pending', 'Failed', 'Completed'])->default('pending');

            // Subscription-related fields
            $table->boolean('is_subscription')->default(false);
            $table->string('billing_cycle')->nullable();
            $table->timestamp('subscription_start_date')->nullable();
            $table->timestamp('subscription_end_date')->nullable();

            // Source info
            $table->unsignedBigInteger('source_id');
            $table->string('source_name');

            $table->timestamps();

            // Indexes
            $table->index(['buyer_id', 'buyer_type']);
            $table->index(['seller_id', 'seller_type']);
            $table->index(['source_id', 'source_name']);
            $table->index('building_id');
            $table->index('unit_id');
            $table->index('gateway_payment_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
