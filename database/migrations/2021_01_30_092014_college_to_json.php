<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CollegeToJson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        Schema::table('users', function (Blueprint $table) {
            $table->json('college')->change();
        });
        

        //I updated the SQL manually here, since no matter what I tried the quotations kept escaping the statement.
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $statement ="UPDATE users SET college = college->".'"$.primary"'." WHERE id > 0";
        DB::statement($statement);

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('college')->change();
        });

        
    }
}
