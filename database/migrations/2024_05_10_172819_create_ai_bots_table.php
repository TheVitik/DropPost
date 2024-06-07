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
        Schema::create('ai_bots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            //$table->unsignedBigInteger('project_id');
            $table->foreignUuid('project_id')
                ->references('id')
                ->on('projects')
                ->cascadeOnDelete();
            $table->string('topic');
            $table->string('keywords');
            $table->string('prompt');
            //$table->unsignedBigInteger('post_template_id')->nullable();
            /*$table->foreignUuid('post_template_id')
                ->nullable()
                ->references('id')
                ->on('post_templates')
                ->nullOnDelete();*/
            $table->boolean('is_generated_photos')->default(false);
            $table->boolean('is_real_photos')->default(false);
            $table->text('post_planning');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_bots');
    }
};
