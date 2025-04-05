<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->boolean('is_admin_transaction')->default(false);
            $table->string('stripe_payment_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('PKR');
            $table->enum('status', ['pending', 'failed', 'completed'])->default('pending');
            $table->boolean('is_subscription')->default(false);
            $table->string('stripe_subscription_id')->nullable()->index();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('unit_id')->references('id')->on('buildingunits')->onDelete('cascade');
            $table->index(['user_id', 'organization_id', 'building_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
