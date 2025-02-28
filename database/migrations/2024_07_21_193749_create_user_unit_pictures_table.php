<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('userunitpictures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_unit_id');
            $table->string('file_path',255)->nullable();
            $table->string('file_name',255)->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('userunitpictures');
    }
};
