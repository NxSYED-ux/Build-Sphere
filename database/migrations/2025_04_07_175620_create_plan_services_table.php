<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planservicecatalog', function (Blueprint $table) {
            $table->id();

            $table->string('title', 255);
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
        });

        Schema::create('planservices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_catalog_id')->constrained('planservicecatalog')->onDelete('cascade');

            $table->integer('quantity')->default(0);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planservices');
        Schema::dropIfExists('planservicecatalog');
    }
};
