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
        Schema::create('userpermissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->unsignedInteger('permission_id')->nullable();  
            $table->tinyInteger('status')->default(1);  
            $table->unsignedBigInteger('granted_by')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userpermissions');
    }
};
