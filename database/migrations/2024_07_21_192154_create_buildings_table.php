<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->string('name','100')->unique();
            $table->string('building_type', 50)->comment('Residential, Commercial, Industrial, Mixed-Use');
            $table->string('status',50)->comment('Approved, Under Review, Rejected, Under Processing, Reapproved');
            $table->text('remarks')->nullable();
            $table->decimal('area', 10, 2);
            $table->year('construction_year')->nullable();
            $table->unsignedBigInteger('address_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('address_id')->references('id')->on('address');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
