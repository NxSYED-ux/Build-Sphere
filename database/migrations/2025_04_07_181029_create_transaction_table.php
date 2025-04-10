<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();

            $table->boolean('is_admin_transaction')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');

            // For fast tracking only otherwise no need to include them
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained('buildingunits')->onDelete('cascade');

            $table->string('transaction_title');
            $table->string('transaction_category'); // New, Adjustment
            $table->string('admin_transaction_type')->nullable(); // Debit (Expense), Credit (Loss)
            $table->string('organization_transaction_type')->nullable();
            $table->string('user_transaction_type')->nullable();

            $table->string('payment_method')->default('Cash');
            $table->string('gateway_payment_id')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('PKR');
            $table->enum('status', ['pending', 'failed', 'completed'])->default('pending');

            $table->boolean('is_subscription')->default(false);
            $table->string('billing_cycle');
            $table->timestamp('subscription_start_date')->nullable();
            $table->timestamp('subscription_end_date')->nullable();

            // For further details
            $table->unsignedBigInteger('source_id');
            $table->string('source_name');

            $table->timestamps();

            $table->index('user_id');
            $table->index(['source_id', 'source_name']);
            $table->index('organization_id');
            $table->index('building_id');
            $table->index('unit_id');
            $table->index('gateway_payment_id');
            $table->index('status');
            $table->index('is_admin_transaction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
