<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create marker types table
        Schema::create("relationship_markers", function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('type');
        });

        DB::table("relationship_markers")->insert(array(
            array("type" => "None"),
            array("type" => "Arrow"),
            array("type" => "Arrow Filled"),
            array("type" => "Diamond"),
            array("type" => "Diamond Filled")));
    
        // Create line type table
        Schema::create("relationship_lines", function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('type');
        });

        DB::table("relationship_lines")->insert(array(
            array("type" => "Solid"),
            array("type" => "Dotted")));


        // Create relationships table
        Schema::create("relationships", function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('starting_class_id')->unsigned();
            $table->integer('ending_class_id')->unsigned();
            $table->integer('starting_marker_id')->unsigned();
            $table->integer('ending_marker_id')->unsigned();
            $table->integer('line_id')->unsigned();
            $table->timestamps();

            $table->foreign('starting_class_id')->references('id')->on('classes');
            $table->foreign('ending_class_id')->references('id')->on('classes');
            $table->foreign('starting_marker_id')->references('id')->on('relationship_markers');
            $table->foreign('ending_marker_id')->references('id')->on('relationship_markers');
            $table->foreign('line_id')->references('id')->on('relationship_lines');
        });

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
