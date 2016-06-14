<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->integer('user_id')->unsigned();
            $table->string("language");
            $table->integer('project_type_id')->unsigned();
            $table->timestamps();

            $table->unique(array('name', 'user_id'));
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_type_id')->references('id')->on('project_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("users");
        Schema::drop("project_types");
        Schema::drop("projects");
    }
}
