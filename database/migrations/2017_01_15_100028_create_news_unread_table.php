<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsUnreadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_unread', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('news_id');
            $table->unsignedInteger('device_id');

            $table->foreign('news_id')->references('id')->on('news')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::drop('news_unread');
    }
}
