<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTypeLanguageTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Create project_types table
        Schema::create("project_types", function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->timestamps();
        });

         // Insert "github" and "empty" project types
        DB::table("project_types")->insert(["name"=>"github"]);
        DB::table("project_types")->insert(["name"=>"empty"]);

        // Create languages table
        Schema::create("languages", function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->boolean('enabled');
            $table->timestamps();
        });

        // Insert Java and PHP into languages
        DB::table("languages")->insert([
            array(
                "name"     => "Java",
                "enabled"  =>  true
            ),
            array(
                "name"     => "PHP",
                "enabled"  => false
            ),
            array(
                "name"     => "None",
                "enabled"  => true
            )
            ]);

        // Create projects table
        Schema::create("projects", function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('repo');
            $table->integer('user_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->integer('project_type_id')->unsigned();
            $table->timestamps();

            $table->unique(array('name', 'user_id'));
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_type_id')->references('id')->on('project_types');
            $table->foreign('language_id')->references('id')->on('languages');
        });
    }
    
     /**
     * Reverse the migrations.
     *
     * @return em
     */
    public function down()
    {
        Schema::drop("project_types");
        Schema::drop("languages");
        Schema::drop("projects");
    }
}
