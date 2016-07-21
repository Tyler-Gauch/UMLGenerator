<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create("classes", function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('locationX');
            $table->integer('locationY');
            $table->string('name');
            $table->string('type');
            $table->string('package');
        });

        Schema::create("operations", function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('class_id')->unsigned();
            $table->string('name');
            $table->string('visibility');
            $table->string('return_type');
            $table->string('parameters');

            $table->foreign('class_id')->references('id')->on('classes');
        });

        Schema::create("attributes", function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('class_id')->unsigned();
            $table->string('name');
            $table->string('visibility');
            $table->string('type');
            $table->string('default_value');

            $table->foreign('class_id')->references('id')->on('classes');
        });

        Schema::create("modelTypes", function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
        });

        Schema::create("models", function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('branch');
            $table->integer('model_id')->unsigned();

            $table->foreign('model_id')->references('id')->on('modelTypes');
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
