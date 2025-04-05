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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_item_id')->constrained('subscription_items')->onDelete('cascade');
            $table->enum('type', ['monthly', 'yearly', 'lifetime'])->default('monthly');
            $table->string('stripe_id')->unique();
            $table->string('stripe_status');
            $table->string('stripe_price')->nullable();
            $table->string('currency')->default('PKR');
            $table->integer('quantity')->default(1);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['user_id', 'stripe_status']);
            $table->index(['stripe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
