<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('video_id')->constrained('videos')->onDelete('cascade');
            $table->timestamp('completed_at')->nullable();
            $table->string('certificate_token')->nullable();
            $table->timestamps();
            $table->unique(['user_id','video_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_completions');
    }
};
