<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('event_categories');
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->string('title');
            $table->text('description');
            $table->integer('minimum_required');
            $table->integer('max_needed');
            $table->boolean('active');
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('zip_code',5);
            $table->string('meeting_type');
            $table->string('meeting_link')->nullable();
            $table->string('meeting_id');
            $table->string('meeting_passcode')->nullable();
            $table->integer('number_of_participants');
            $table->integer('rsvp_yes')->nullable();
            $table->integer('rsvp_interested')->nullable();
            $table->integer('comment_count')->nullable();
            $table->string('comment_table_id_link')->nullable();
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
        Schema::dropIfExists('events');
    }
}
