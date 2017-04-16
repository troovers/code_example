<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('poll_id');
            $table->string('answer');

            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('poll_answers');
    }
}
