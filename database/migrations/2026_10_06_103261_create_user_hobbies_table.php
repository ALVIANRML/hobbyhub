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
        Schema::create('user_hobbies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_users')
                ->constrained('users', 'id')
                ->onDelete('cascade');
            $table->foreignId('id_hobbies')
                ->constrained('hobbies')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_hobbies');
    }
};
