<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planSubscriptionItems', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('service_id');

            $table->string('service_name');
            $table->string('service_keyword');
            $table->integer('quantity');

            $table->integer('used')->default(0);

            $table->foreign('subscription_id')->references('id')->on('planSubscriptions')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('planServices')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planSubscriptionItems');
    }
};
