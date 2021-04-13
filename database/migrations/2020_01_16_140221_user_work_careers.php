<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserWorkCareers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_work_careers', function (Blueprint $table) {
            $table->bigInteger('uid')->unique()->unsigned();
            $table->integer('work_for')->nullable();
            $table->boolean('privacy_business_city')->nullable();
            $table->boolean('privacy_travel_city')->nullable();
            $table->integer('employment_status')->nullable();
            $table->integer('work_title')->nullable();
            $table->integer('work_title_scale')->nullable();
            $table->boolean('hire_full')->nullable();
            $table->integer('hire_full_count')->nullable();
            $table->boolean('hire_full_looking')->nullable();
            $table->integer('hire_full_for')->nullable();
            $table->boolean('privacy_hire_full')->default(false);
            $table->boolean('hire_gig')->nullable();
            $table->integer('hire_gig_count')->nullable();
            $table->boolean('privacy_hire_gig')->default(false);
            $table->boolean('hire_intern')->nullable();
            $table->integer('hire_intern_count')->nullable();
            $table->boolean('hire_intern_looking')->nullable();
            $table->integer('hire_intern_for')->nullable();
            $table->boolean('privacy_hire_intern')->default(false);
            $table->integer('own_business')->nullable();
            $table->boolean('seeking_investment')->nullable();
            $table->integer('buying_stuff')->nullable();
            $table->integer('customer')->nullable();
            $table->boolean('investor')->nullable();
            $table->integer('wealth')->nullable();
            $table->integer('wealth_scale')->nullable();
            $table->integer('review_plan')->nullable();
            $table->boolean('privacy_investor')->default(false);

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
        Schema::dropIfExists('user_work_careers');
    }
}
