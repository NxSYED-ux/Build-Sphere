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
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('granted_by');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->foreign('granted_by')->references('id')->on('users');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rolepermissions');
    }
};
