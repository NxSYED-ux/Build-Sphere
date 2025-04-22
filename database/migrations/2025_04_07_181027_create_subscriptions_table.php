<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_payment_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');

            // For fast tracking only otherwise no need to include them
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained('buildingunits')->onDelete('cascade');

            $table->unsignedBigInteger('source_id');
            $table->string('source_name');

            $table->string('billing_cycle');
            $table->string('subscription_status'); // e.g., "Active", "Canceled"
            $table->decimal('price_at_subscription', 10, 2);
            $table->string('currency_at_subscription');

            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable(); // Actual subscription end date

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['user_id', 'source_name', 'source_id']);
            $table->index('organization_id');
            $table->index('building_id');
            $table->index('unit_id');
            $table->index(['billing_cycle', 'subscription_status']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
