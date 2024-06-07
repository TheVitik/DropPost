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
        Schema::create('channels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description');
            $table->bigInteger('telegram_chat_id');
            //$table->unsignedBigInteger('project_id');
            $table->foreignUuid('project_id')
                ->references('id')
                ->on('projects')
                ->cascadeOnDelete();
            $table->integer('members_count')->default(0);
            $table->string('type');
            $table->string('username')->nullable();
            $table->string('invite_link')->nullable();
            $table->string('photo_path')->nullable();
            // Settings
            $table->boolean('is_bot_active')->default(false);
            $table->boolean('is_automessage_active')->default(false);
            $table->boolean('is_copy_active')->default(false);
            //$table->unsignedBigInteger('ai_bot_id')->nullable();
            $table->foreignUuid('ai_bot_id')
                ->nullable()
                ->references('id')
                ->on('ai_bots')
                ->nullOnDelete();
            $table->text('automessage')->nullable();
            //$table->bigInteger('copy_telegram_chat_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
