<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('uid');
            $table->integer('parents_from_alma')->nullable();
            $table->integer('siblings_from_alma')->nullable();
            $table->integer('play_video_games')->nullable();
            $table->integer('video_games_frequency')->nullable();
            $table->integer('video_games_categories')->nullable();
            $table->string('video_games_fav_title')->nullable();
            $table->string('athletic_stuff_you_play')->nullable();
            $table->integer('have_exotic_pet')->nullable();
            $table->integer('havePet')->nullable();
            $table->integer('pet')->nullable();
            $table->integer('fan_of_alma_football')->nullable();
            $table->integer('fan_of_alma_basketball')->nullable();
            $table->integer('in_us_military')->nullable();
            $table->integer('military_type')->nullable();
            $table->string('military_code')->nullable();
            $table->integer('military_rank')->nullable();
            $table->integer('dependent_us_military_person')->nullable();
            $table->integer('instrument')->nullable();
            $table->integer('long_have_lived_here')->nullable();
            $table->string('country_to_travel')->nullable();
            $table->string('state_to_travel')->nullable();
            $table->string('city_to_travel')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_activities');
    }
}
