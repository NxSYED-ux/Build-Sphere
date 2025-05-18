<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_cycles', function (Blueprint $table) {
            $table->id();

            $table->integer('duration_months');
            $table->string('description')->nullable();

        });

        Schema::create('planserviceprices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_id')->constrained('planservices')->onDelete('cascade');
            $table->foreignId('billing_cycle_id')->constrained('billing_cycles')->onDelete('cascade');

            $table->decimal('price', 10, 2);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['service_id', 'billing_cycle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planserviceprices');
        Schema::dropIfExists('billing_cycles');
    }
};

