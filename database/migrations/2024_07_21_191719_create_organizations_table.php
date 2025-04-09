<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name',50)->unique();
            $table->unsignedBigInteger('address_id')->unique();
            $table->enum('status', ['Enable', 'Disable', 'Block'])->default('Enable');
            $table->date('membership_start_date')->nullable();
            $table->date('membership_end_date')->nullable();
            $table->unsignedBigInteger('owner_id')->unique();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('address_id')->references('id')->on('address');
            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
