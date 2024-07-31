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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('owner_id');
            $table->boolean('is_answered');
            $table->integer('view_count');
            $table->bigInteger('accepted_answer_id')->nullable();
            $table->integer('answer_count');
            $table->integer('score');
            $table->bigInteger('last_activity_date');
            $table->bigInteger('creation_date');
            $table->bigInteger('last_edit_date')->nullable();
            $table->bigInteger('question_id')->unique();
            $table->string('content_license')->nullable();
            $table->string('link');
            $table->string('title');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
