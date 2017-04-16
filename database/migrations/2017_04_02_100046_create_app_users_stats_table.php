<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppUsersStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_users_stats', function (Blueprint $table) {
            $table->date('date');
            $table->integer('youth_members')->nullable();
            $table->integer('senior_members')->nullable();

            $table->primary('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('app_users_stats');
    }
}
