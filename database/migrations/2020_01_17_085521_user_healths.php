<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserHealths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_healths', function (Blueprint $table) {
            $table->bigInteger('uid')->unsigned()->unique();
            $table->integer('mental')->nullable();
            $table->boolean('mental_privacy')->nullable();
            $table->integer('physical')->nullable();
            $table->boolean('physical_privacy')->nullable();

            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_healths');
    }
}