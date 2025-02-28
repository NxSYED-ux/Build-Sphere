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
            $table->unsignedBigInteger('level_id');
            $table->unsignedBigInteger('building_id');
            $table->unsignedBigInteger('organization_id');
            $table->string('unit_name',50);
            $table->string('unit_type',50)->comment('Room, Shop, Apartment, Restaurant, Gym');
            $table->enum('availability_status', ['Available', 'Rented', 'Sold', 'Not Available']);
            $table->enum('sale_or_rent', ['Sale', 'Rent', 'Not Available']);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('area', 10, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['Approved', 'Rejected']);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildingunits');
    }
};
