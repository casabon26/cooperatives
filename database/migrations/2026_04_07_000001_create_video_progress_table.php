<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_progresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('video_id');
            $table->double('current_time')->default(0);
            $table->double('total_duration')->default(0);
            $table->integer('progress_percent')->default(0);
            $table->timestamps();
            $table->unique(['user_id','video_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_progresses');
    }
};
