<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->unsignedInteger('poll_id');
            $table->unsignedInteger('answer_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->dateTime('date');

            $table->primary(['poll_id', 'answer_id', 'date']);

            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('answer_id')->references('id')->on('poll_answers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('poll_votes');
    }
}
