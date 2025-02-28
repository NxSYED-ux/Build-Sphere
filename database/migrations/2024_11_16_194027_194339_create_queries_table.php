<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('building_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('staff_member_id');
            $table->text('description');
            $table->enum('status', ['Open', 'In Progress', 'Closed', 'Rejected']);
            $table->dateTime('expected_closure_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queries');
    }
};
