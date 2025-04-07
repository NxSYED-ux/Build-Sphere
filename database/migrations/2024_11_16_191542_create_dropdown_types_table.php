<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dropDownTypes', function (Blueprint $table) {
            $table->unsignedInteger('id',true);
            $table->string('type_name')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('parent_type_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('parent_type_id')->references('id')->on('dropDownTypes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dropDownTypes');
    }
};
