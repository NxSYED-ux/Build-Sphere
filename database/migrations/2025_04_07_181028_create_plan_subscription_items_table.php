<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plansubscriptionitems', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_catalog_id')->constrained('planservicecatalog')->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('used')->default(0);
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planSubscriptionItems');
    }
};
