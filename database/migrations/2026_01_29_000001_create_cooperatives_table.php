<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cooperatives', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('sector')->nullable()->index();
            $table->string('region')->nullable()->index();
            $table->text('description')->nullable();
            $table->enum('status', ['pending','active','suspended','archived'])->default('pending')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cooperatives');
    }
};
