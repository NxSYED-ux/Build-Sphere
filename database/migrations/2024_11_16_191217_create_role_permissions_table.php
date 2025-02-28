<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rolepermissions', function (Blueprint $table) {
            $table->unsignedInteger('id',true);
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('permission_id');
            $table->string('name', 255);
            $table->string('header', 255);
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('granted_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rolepermissions');
    }
};
