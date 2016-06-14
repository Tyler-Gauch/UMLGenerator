<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("project_types", function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('name');
        });

        DB::table("project_types")->insert(["name"=>"github"], ["name"=>"empty"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
