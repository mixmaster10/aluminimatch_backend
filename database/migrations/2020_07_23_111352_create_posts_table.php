<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("title");
            $table->text("description")->nullable();
            $table->text("summary")->nullable();
            $table->string("photoUrl")->nullable();
            $table->bigInteger('postTypeId')->nullable()->unsigned();
            $table->bigInteger('postCategoryId')->nullable()->unsigned();
            $table->bigInteger('userId')->nullable()->unsigned();
            $table->timestamps();
            $table->text("embed")->nullable();

            $table->foreign('postTypeId')->references('id')->on('post_types')->onDelete('cascade');
            $table->foreign('postCategoryId')->references('id')->on('post_categories')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
