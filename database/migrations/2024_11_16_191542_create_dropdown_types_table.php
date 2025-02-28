<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dropdowntypes', function (Blueprint $table) {
            $table->unsignedInteger('id',true);
            $table->string('type_name')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_type_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dropdowntypes');
    }
};
