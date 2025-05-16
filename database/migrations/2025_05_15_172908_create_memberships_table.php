<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('building_id')->nullable();
            $table->unsignedBigInteger('organization_id');
            $table->string('image')->default('uploads/memberships/images/defaultImage.jpeg');
            $table->string('name', 100);
            $table->string('url');
            $table->text('description')->nullable();
            $table->enum('category', ['GYM', 'Restaurant', 'Other']);
            $table->integer('duration_months');
            $table->integer('scans_per_month');
            $table->boolean('mark_as_featured')->default(false);
            $table->string('currency', 10)->default('PKR');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->enum('status', ['Draft', 'Published', 'Non Renewable', 'Archived'])->default('Draft');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('unit_id')->references('id')->on('buildingunits');
            $table->foreign('building_id')->references('id')->on('buildings');
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->unique(['unit_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
