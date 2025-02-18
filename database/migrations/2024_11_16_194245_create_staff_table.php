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
        Schema::create('staff', function (Blueprint $table) { 
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->unsignedBigInteger('department_id')->nullable();  
            $table->unsignedBigInteger('building_id')->nullable();
            $table->tinyInteger('is_manager')->default(0); 
            $table->unsignedInteger('active_load')->default(0);  //new
            $table->unsignedBigInteger('assigned_by')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
