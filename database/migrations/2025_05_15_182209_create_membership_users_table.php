<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('membership_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('cascade');

            $table->boolean('status')->default(true);
            $table->integer('quantity')->default(1);
            $table->integer('used')->default(0);
            $table->dateTime('ends_at');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['user_id', 'membership_id', 'subscription_id']);
        });

        Schema::create('membership_usage_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('membership_user_id')->constrained('membership_users')->onDelete('cascade');
            $table->date('usage_date');
            $table->integer('used')->default(1);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['membership_user_id', 'usage_date']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('membership_usage_logs');
        Schema::dropIfExists('membership_users');
    }
};
