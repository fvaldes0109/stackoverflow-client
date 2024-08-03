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
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->nullable();
            $table->integer('reputation')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('user_type');
            $table->integer('accept_rate')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('display_name')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};
