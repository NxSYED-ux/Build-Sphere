<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planServices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');

            $table->string('name'); // For frontend: e.g., "Manage Buildings"
            $table->string('keyword'); // For backend: e.g., "buildings"
            $table->integer('quantity')->nullable(); // Null = unlimited
            $table->boolean('status')->default(true);
            $table->json('meta')->nullable();  // Optional: for extra service-specific options

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planServices');
    }
};
