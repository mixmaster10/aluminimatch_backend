<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDegreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_degrees', function (Blueprint $table) {
            $table->bigInteger('uid')->unsigned();
            $table->integer('type');
            $table->bigInteger('degree')->unsigned();
            $table->bigInteger('ibc')->unsigned()->nullable();
            $table->string('year');

            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('degree')->references('id')->on('degrees')->onDelete('cascade');
            $table->foreign('ibc')->references('id')->on('ibcs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_degrees');
    }
}
