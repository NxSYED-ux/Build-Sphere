<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->tinyInteger('is_admin_subscription')->default(0);
            $table->string('stripe_id')->unique();
            $table->string('stripe_product');
            $table->string('stripe_price');
            $table->string('currency')->default('PKR');
            $table->integer('quantity')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['stripe_id', 'stripe_price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_items');
    }
};
