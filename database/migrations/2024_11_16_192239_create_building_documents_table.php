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
        Schema::create('buildingdocuments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('building_id');  
            $table->string('document_type');  
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('file_path')->nullable();  
            $table->string('file_name')->nullable();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildingdocuments');
    }
};
