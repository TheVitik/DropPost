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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('channel_id');
            $table->foreign('channel_id')
                ->references('id')
                ->on('channels')
                ->cascadeOnDelete();
            $table->bigInteger('telegram_message_id')->nullable();
            $table->text('content_json');
            $table->text('content_html');
            $table->boolean('is_advertisement')->default(false);
            $table->boolean('is_draft')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
