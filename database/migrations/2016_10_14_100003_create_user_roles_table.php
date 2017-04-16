<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');
            $table->integer('view')->default(0);
            $table->integer('add')->default(0);
            $table->integer('edit')->default(0);
            $table->integer('delete')->default(0);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_roles');
    }
}
