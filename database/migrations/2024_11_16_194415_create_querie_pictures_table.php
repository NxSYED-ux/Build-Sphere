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
        Schema::create('queriepictures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('query_id');  
            $table->string('file_path',255)->nullable();  
            $table->string('file_name',255)->nullable();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::dropIfExists('queriepictures');
    }
};
