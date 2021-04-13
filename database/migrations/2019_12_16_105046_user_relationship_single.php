<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserRelationshipSingle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_relationship_single', function(Blueprint $table) {
            $table->bigInteger('uid')->unsigned()->unique();
            $table->boolean('meet_divorced')->default(false);
            $table->integer('single_scale')->nullable();
            $table->integer('ethnicity')->nullable();
            $table->integer('music')->nullable();
            $table->integer('drink')->nullable();
            $table->boolean('privacy_drink')->default(false);
            $table->tinyInteger('smoke')->nullable();
            $table->boolean('privacy_smoke')->default(false);
            $table->integer('sex_scale')->nullable();
            $table->tinyInteger('have_pets')->nullable();
            $table->integer('pets')->nullable();
            $table->integer('pets_scale')->nullable();
            $table->integer('like_pets')->nullable();
            $table->integer('match_age')->nullable();
            $table->integer('bodytype')->nullable();
            $table->boolean('privacy_body_type')->default(false);
            $table->integer('own_body_type')->nullable();
            $table->boolean('privacy_own_body_type')->default(false);
            $table->integer('laugh')->nullable();
            $table->boolean('privacy_laugh')->default(false);
            $table->integer('laugh_scale')->nullable();
            $table->tinyInteger('married_before')->nullable();
            $table->integer('married_count')->nullable();
            $table->integer('married_scale')->nullable();
            $table->boolean('have_kids')->default(false);
            $table->integer('kids_scale')->nullable();


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
        Schema::dropIfExists('user_relationship_single');
    }
}
