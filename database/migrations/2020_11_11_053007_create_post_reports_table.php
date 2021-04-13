<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('postId');
            $table->unsignedBigInteger('reportedBy');
            $table->text('reason');
            $table->timestamps();

            $table->foreign('reportedBy')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('postId')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_reports');
    }
}
