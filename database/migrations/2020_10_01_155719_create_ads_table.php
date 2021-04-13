<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('leadsRemaining');
            $table->unsignedBigInteger('totalLeads');
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->boolean('active');
            $table->unsignedBigInteger('comment_count')->nullable();
            $table->boolean('isLiked')->nullable();
            $table->unsignedBigInteger('likes_count')->nullable();
            $table->string('websiteLink')->nullable();
            $table->string('audience')->nullable();
            $table->string('photoUrl')->nullable();
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
        Schema::dropIfExists('ads');
    }
}
