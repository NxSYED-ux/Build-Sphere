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
        Schema::create('userbuildingunits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  
            $table->unsignedBigInteger('unit_id'); 
            $table->date('rent_start_date')->nullable(); 
            $table->date('rent_end_date')->nullable(); 
            $table->date('purchase_date')->nullable();
            $table->tinyInteger('contract_status');  //new
            $table->enum('type', ['Rented', 'Sold']);  //new
            $table->decimal('price', 10, 2)->nullable()->default(0);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userbuildingunits');
    }
};
