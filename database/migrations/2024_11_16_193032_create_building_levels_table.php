<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buildinglevels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('building_id');  
            $table->string('level_name',50);  
            $table->text('description')->nullable();
            $table->integer('level_number');
            $table->enum('status', ['Approved', 'Rejected']); 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildinglevels');
    }
};
