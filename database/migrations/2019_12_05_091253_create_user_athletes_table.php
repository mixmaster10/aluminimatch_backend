<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAthletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_athletes', function (Blueprint $table) {
            $table->bigInteger('uid')->unsigned()->unique();
            $table->tinyInteger('member');
            $table->bigInteger('athlete')->unsigned()->nullable();
            $table->integer('position')->nullable();
            $table->boolean('privacy')->default(true);

            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('athlete')->references('id')->on('athletes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_athletes');
    }
}
