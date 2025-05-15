<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('membership_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->integer('used')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'membership_id', 'subscription_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_users');
    }
};
