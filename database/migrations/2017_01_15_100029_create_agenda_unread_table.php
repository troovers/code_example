<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgendaUnreadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agenda_unread', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('agenda_id');
            $table->unsignedInteger('device_id');

            $table->foreign('agenda_id')->references('id')->on('agenda')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('device_id')->references('id')->on('app_devices')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('agenda_unread');
    }
}
