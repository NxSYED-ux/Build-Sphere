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

            $table->string('name','100')->unique();
            $table->string('building_type', 50)->comment('Residential, Commercial, Industrial, Mixed-Use');
            $table->string('status',50)->comment('Approved, Under Review, Rejected, Under Processing, For Re-Approval');
            $table->text('remarks')->nullable();
            $table->decimal('area', 10, 2);
            $table->year('construction_year')->nullable();
            $table->tinyInteger('isFreeze')->default(0);

            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->unique()->constrained('address')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
