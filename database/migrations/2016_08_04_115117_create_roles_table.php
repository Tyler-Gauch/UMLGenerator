<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         // Create project_types table
        Schema::create("roles", function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->timestamps();
        });

         // Create project_types table
        Schema::create("user_roles", function(Blueprint $table){
            $table->increments('id')->unsigned();
            $table->integer("user_id")->unsigned();
            $table->integer("role_id")->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('role_id')->references('id')->on('roles');
        });

        DB::table("roles")->insert(["name" => "ADMIN"]);
        DB::table("roles")->insert(["name" => "USER"]);
        DB::table("roles")->insert(["name" => "GUEST"]);

        $roleUser = DB::table("roles")->where("name", "=", "GUEST")->first();
        $user = DB::table("users")->where("username", "=", "UML_Guest")->first();

        DB::table("user_roles")->insert(["user_id" => $user->id, "role_id" => $roleUser->id]);
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
