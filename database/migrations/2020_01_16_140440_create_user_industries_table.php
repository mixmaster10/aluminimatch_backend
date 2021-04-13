<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserIndustriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_industries', function (Blueprint $table) {
            $table->bigInteger('uid')->unsigned();
            $table->bigInteger('industry')->unsigned();

            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('industry')->references('id')->on('industries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_industries');
    }
}
