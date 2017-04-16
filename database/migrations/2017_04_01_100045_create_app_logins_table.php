<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_logins', function (Blueprint $table) {
            $table->dateTime('logged_in');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('device_token');
            $table->string('platform')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->integer('weekday');

            $table->primary(['logged_in', 'device_token']);

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
        Schema::drop('app_logins');
    }
}
