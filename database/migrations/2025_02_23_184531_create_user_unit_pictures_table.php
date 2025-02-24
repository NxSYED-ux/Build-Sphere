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
        Schema::create('user_unit_pictures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_unit_id');  
            $table->string('file_path',255)->nullable();  
            $table->string('file_name',255)->unique()->nullable();  
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
        Schema::dropIfExists('user_unit_pictures');
    }
};
