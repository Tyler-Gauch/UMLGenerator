<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixArrowFill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table("relationship_markers")->where("type", "=", "arrowFilled")->delete();
        DB::table("relationship_markers")->insert(["type" => "arrowFill"]);
        DB::table("relationship_markers")->where("type", "=", "diamondFilled")->delete();
        DB::table("relationship_markers")->insert(["type" => "diamondFill"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
