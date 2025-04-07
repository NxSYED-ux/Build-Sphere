<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planSubscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');

            $table->string('type'); // Type of subscription (e.g., "Free", "Premium")
            $table->string('billing_cycle'); // E.g., "monthly", "yearly"

            $table->string('stripe_customer_id')->unique();
            $table->string('stripe_status'); // e.g., "active", "canceled"
            $table->string('stripe_price')->nullable(); // Total Price at the time of subscription
            $table->string('currency')->default('PKR');

            $table->timestamp('trial_ends_at')->nullable(); // Trial period end date, if applicable
            $table->timestamp('ends_at')->nullable(); // Actual subscription end date

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['user_id', 'plan_id', 'stripe_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planSubscriptions');
    }
};
