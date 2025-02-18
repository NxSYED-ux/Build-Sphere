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
        Schema::create('queries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');   
            $table->unsignedBigInteger('building_id');   
            $table->unsignedBigInteger('department_id');   
            $table->unsignedBigInteger('staff_id');   
            $table->text('description');    
            $table->enum('status', ['open', 'in_progress', 'closed']); 
            $table->date('expected_closure_date')->nullable();        //new
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queries');
    }
};
