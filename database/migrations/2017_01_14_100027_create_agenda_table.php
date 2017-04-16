<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgendaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agenda', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->text('description');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('address')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('city')->nullable();
            $table->integer('notification_days')->nullable();
            $table->integer('notification')->default(0);

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
        Schema::drop('agenda');
    }
}
