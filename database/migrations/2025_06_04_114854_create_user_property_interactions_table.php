<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_property_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained('buildingunits')->onDelete('cascade');
            $table->enum('interaction_type', ['view', 'favourite', 'contact'])->default('view');
            $table->timestamp('timestamp')->useCurrent();

            $table->index(['user_id', 'unit_id']);
            $table->index('interaction_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_property_interactions');
    }
};
