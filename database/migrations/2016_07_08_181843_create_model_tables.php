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

        Schema::create("models", function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('branch')->nullable();
            $table->integer('model_id')->unsigned();
            $table->integer('project_id')->unsigned();

            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::create("classes", function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('model_id')->unsigned();
            $table->integer('locationX');
            $table->integer('locationY');
            $table->string('name');
            $table->string('type');
            $table->string('package')->nullable();

            $table->timestamps();

            $table->foreign('model_id')->references('id')->on('models');
        });

        Schema::create("operations", function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('class_id')->unsigned();
            $table->string('name');
            $table->string('visibility');
            $table->string('return_type');
            $table->string('parameters')->nullable();
            $table->boolean('is_static')->default(false);
            $table->boolean('is_final')->default(false);
            $table->boolean('is_abstract')->default(false);

            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('classes');
        });

        Schema::create("attributes", function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer('class_id')->unsigned();
            $table->string('name');
            $table->string('visibility');
            $table->string('type');
            $table->string('default_value')->nullable();
            $table->boolean('is_static')->default(false);
            $table->boolean('is_final')->default(false);
            $table->boolean('is_abstract')->default(false);

            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('classes');
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
