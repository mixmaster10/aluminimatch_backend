<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserReligions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_religions', function (Blueprint $table) {
            $table->bigInteger('uid')->unsigned();
            $table->integer('religion');
            $table->integer('church')->nullable();
            $table->integer('year')->nullable();
            $table->integer('dating_scale')->nullable();
            $table->integer('friendship_scale')->nullable();
            $table->integer('work_scale')->nullable();
            $table->integer('spiritual_scale')->nullable();
            $table->integer('general_scale')->nullable();

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
        Schema::dropIfExists('user_religions');
    }
}
