<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name',100);
            $table->string('email',255)->unique();
            $table->string('password',255);
            $table->string('phone_no',20)->nullable();
            $table->string('cnic',25)->unique();
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('picture',255)->nullable();
            $table->tinyInteger('is_super_admin')->default(0);
            $table->tinyInteger('is_verified')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->text('reset_token')->nullable();
            $table->string('customer_payment_id')->nullable()->index();
            $table->dateTime('last_login')->nullable();

            $table->unsignedInteger('role_id'); $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreignId('address_id')->unique()->constrained('address')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('role_id');
            $table->index('is_super_admin');
            $table->index('status');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
