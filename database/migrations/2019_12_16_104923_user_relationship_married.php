<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserRelationshipMarried extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_relationship_married', function(Blueprint $table) {
            $table->bigInteger('uid')->unsigned()->unique();
            $table->boolean('is_alumni')->default(false);
            $table->integer('meet_couple_scale')->nullable();
            $table->integer('year')->nullable();
            $table->boolean('privacy_married_year')->nullable();
            $table->boolean('have_kids')->default(false);
            $table->integer('meet_kid_scale')->nullable();
            $table->integer('meet_married_scale')->nullable();

            $table->string('plan_marry_date')->nullable();
            $table->tinyInteger('finance')->nullable();

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
        Schema::dropIfExists('user_relationship_married');
    }
}
