<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stat_team', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('stat_id')->unsigned()->index();
            $table->foreign('stat_id')->references('id')->on('stats')->onDelete('cascade');
            $table->integer('team_id')->unsigned()->index();
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');

            $table->decimal('score_high', 5 , 2)->nullable();
            $table->decimal('score_low', 5 , 2)->nullable();

            $table->decimal('target_low', 5 , 2)->nullable();
            $table->decimal('target_mid', 5 , 2)->nullable();
            $table->decimal('target_high', 5 , 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stat_team');
    }
}
