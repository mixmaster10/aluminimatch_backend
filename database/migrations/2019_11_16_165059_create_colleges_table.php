<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colleges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('country_id')->unsigned();
            $table->bigInteger('state_id')->nullable()->unsigned();
            $table->string('name');
            $table->string('color1')->default('b1b1b1');
            $table->string('color2')->default('ffffff');
            $table->string('logo1')->default('logo1_default.png');
            $table->string('logo2')->default('logo2_default.png');
            $table->string('slogan')->nullable();
            $table->string('acronym')->nullable();
            $table->string('banner')->default('assets/imgs/banner.png');

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('colleges');
    }
}
