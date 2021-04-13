<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMillitariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('millitaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('military_branch')->nullable();
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->string('rank')->nullable();
            $table->string('section')->nullable();
            $table->string('similar_codes')->nullable();
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
        Schema::dropIfExists('millitaries');
    }
}
