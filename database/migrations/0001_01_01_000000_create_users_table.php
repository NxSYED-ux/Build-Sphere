<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->string('email',255)->unique(); 
            $table->string('password',255);
            $table->string('phone_no',20)->nullable();  
            $table->string('cnic',25)->unique()->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('picture',255)->nullable();  
            $table->unsignedInteger('role_id')->default(1);  
            $table->unsignedBigInteger('address_id')->nullable(); 
            $table->tinyInteger('status')->default(1);    //1 for enable and 0 for disable 
            $table->date('date_of_birth'); 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable(); 
            $table->timestamps();
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users'); 
        Schema::dropIfExists('sessions');
    }
};
