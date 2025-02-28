<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dropdownvalues', function (Blueprint $table) {
            $table->unsignedInteger('id',true);
            $table->string('value_name',50)->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('dropdown_type_id');
            $table->unsignedInteger('parent_value_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dropdownvalues');
    }
};
