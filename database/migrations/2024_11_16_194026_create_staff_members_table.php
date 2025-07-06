<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staffmembers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->unique();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade');

            $table->decimal('salary',8,1)->default(0);
            $table->unsignedInteger('active_load')->default(0);
            $table->tinyInteger('accept_queries')->default(1);
            $table->timestamp('joined_at')->nullable();
            $table->tinyInteger('status')->default(1);

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staffmembers');
    }
};
