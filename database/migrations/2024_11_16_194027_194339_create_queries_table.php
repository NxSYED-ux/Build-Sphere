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
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('staff_member_id')->nullable();
            $table->text('description');
            $table->enum('status', ['Open', 'In Progress', 'Closed', 'Rejected', 'Closed Late']);
            $table->dateTime('expected_closure_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('buildingunits')->onDelete('cascade');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('staff_member_id')->references('id')->on('staffmembers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queries');
    }
};
