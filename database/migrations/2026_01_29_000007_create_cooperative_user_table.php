<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cooperative_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperative_id')->constrained('cooperatives')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role')->default('member'); // e.g., admin/member
            $table->timestamps();
            $table->unique(['cooperative_id','user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cooperative_user');
    }
};
