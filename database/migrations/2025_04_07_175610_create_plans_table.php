<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., Free, Pro, Enterprise
            $table->text('description')->nullable();
            $table->string('currency', 10)->default('PKR');
            $table->string('status')->default('Active')->comment('Inactive, Active, Deleted, Custom');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }

};
