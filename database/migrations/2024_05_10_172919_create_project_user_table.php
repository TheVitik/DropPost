<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            //$table->unsignedBigInteger('project_id');
            $table->foreignUuid('project_id')
                ->references('id')
                ->on('projects')
                ->cascadeOnDelete();
            //$table->unsignedBigInteger('user_id');
            $table->foreignUuid('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->json('permissions')->nullable();
            $table->string('role')->default(\App\Enums\UserRole::MEMBER);
            //$table->boolean('is_invited')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
