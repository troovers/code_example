<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_devices', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedInteger('user_id');
            $table->text('token');
            $table->string('name')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('platform')->nullable();
            $table->integer('notifications')->default(1)->nullable();
            $table->dateTime('last_login');

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
        Schema::drop('app_devices');
    }
}
