<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('cooperative_directories');
    }

    public function down()
    {
        Schema::create('cooperative_directories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperative_id')->unique();
            $table->string('name')->nullable();
            $table->string('sector')->nullable()->index();
            $table->string('region')->nullable()->index();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('cooperative_id')->references('id')->on('cooperatives')->onDelete('cascade');
        });
    }
};
