<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEtnicityMusicToArrayUserRelationshipSingle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_relationship_single', function (Blueprint $table) {
            //
            $table->json('ethnicity')->change();
            $table->json('music')->change();
            $table->json('match_age')->change();
        });

        Schema::table('user_work_careers', function (Blueprint $table) {
            //
            $table->json('buying_stuff')->change();
            $table->json('customer')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_relationship_single', function (Blueprint $table) {
            //
        });
    }
}
