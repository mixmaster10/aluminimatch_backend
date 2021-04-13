<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserLearnLanguageScales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_learn_language_scales', function (Blueprint $table) {
            $table->bigInteger('uid')->unsigned();
            $table->integer('fluent')->nullable();
            $table->integer('level')->nullable();
            $table->integer('tutor')->nullable();
            $table->integer('teach')->nullable();

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
        Schema::dropIfExists('user_learn_language_scales');
    }
}
