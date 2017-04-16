<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollsUnvotedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polls_unvoted', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('poll_id');
            $table->unsignedInteger('user_id');

            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('polls_unvoted');
    }
}
