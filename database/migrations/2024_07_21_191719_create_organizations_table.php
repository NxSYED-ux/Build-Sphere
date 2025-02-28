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
            $table->unsignedBigInteger('address_id');
            $table->enum('status', ['Enable', 'Disable', 'Block']);
            $table->date('membership_start_date');
            $table->date('membership_end_date');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
