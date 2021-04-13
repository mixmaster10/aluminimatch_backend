<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOrgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_orgs', function (Blueprint $table) {
            $table->bigInteger('uid')->unsigned();
            $table->bigInteger('org')->unsigned();

            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('org')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_orgs');
    }
}
