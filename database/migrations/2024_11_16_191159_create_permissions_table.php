<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->unsignedInteger('id',true);
            $table->string('name',255)->unique();
            $table->string('header',255);
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedInteger('parent_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('parent_id')->references('id')->on('permissions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
