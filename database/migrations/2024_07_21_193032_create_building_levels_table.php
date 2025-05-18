<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildinglevels', function (Blueprint $table) {
            $table->id();

            $table->string('level_name',50);
            $table->text('description')->nullable();
            $table->integer('level_number');
            $table->enum('status', ['Approved', 'Rejected']);

            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildinglevels');
    }
};
