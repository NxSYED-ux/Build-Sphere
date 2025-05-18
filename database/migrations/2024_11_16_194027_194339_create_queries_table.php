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

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('buildingunits')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('staff_member_id')->nullable()->constrained('staffmembers')->onDelete('set null');

            $table->text('description');
            $table->enum('status', ['Open', 'In Progress', 'Closed', 'Rejected', 'Closed Late']);
            $table->dateTime('expected_closure_date')->nullable();
            $table->dateTime('closure_date')->nullable();
            $table->text('remarks')->nullable();
            $table->decimal('expense',10,2)->default(0);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queries');
    }
};
