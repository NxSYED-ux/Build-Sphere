<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildingunits', function (Blueprint $table) {
            $table->id();

            $table->string('unit_name',50);
            $table->string('unit_type',50)->comment('Room, Shop, Apartment, Restaurant, Gym');
            $table->enum('availability_status', ['Available', 'Rented', 'Sold'])->default('Available');
            $table->enum('sale_or_rent', ['Sale', 'Rent', 'Not Available']);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('area', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['Approved', 'Rejected']);

            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->foreignId('level_id')->constrained('buildinglevels')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildingunits');
    }
};
