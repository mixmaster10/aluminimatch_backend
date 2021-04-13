<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FriendRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friend_requests', function (Blueprint $table) {
            $table->bigInteger('uid')->unsigned();
            $table->bigInteger('fid')->unsigned();
            $table->text('msg')->nullable();
            $table->timestamps();

            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('fid')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('friend_requests');
    }
}
