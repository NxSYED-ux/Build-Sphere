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
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');

            $table->string('image')->default('uploads/memberships/images/defaultImage.jpeg');
            $table->string('name', 100);
            $table->string('url');
            $table->text('description')->nullable();
            $table->enum('category', ['Gym', 'Restaurant', 'Other']);
            $table->integer('duration_months');
            $table->integer('scans_per_day');
            $table->boolean('mark_as_featured')->default(false);
            $table->string('currency', 10)->default('PKR');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('original_price', 10, 2)->default(0);
            $table->enum('status', ['Draft', 'Published', 'Non Renewable', 'Archived'])->default('Draft');

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['unit_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
