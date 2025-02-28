<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('userpermissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('permission_id');
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('granted_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('userpermissions');
    }
};
