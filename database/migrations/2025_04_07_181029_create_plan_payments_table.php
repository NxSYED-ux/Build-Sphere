<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('planPayments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');

            $table->string('stripe_payment_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('PKR');

            $table->enum('status', ['pending', 'failed', 'completed'])->default('pending');

            $table->timestamp('subscription_start_date')->nullable();
            $table->timestamp('subscription_end_date')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['user_id', 'organization_id', 'plan_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('planPayments');
    }
};
