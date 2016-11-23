<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayerStatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_stats', function (Blueprint $table) {
            $table->increments('id');

            $table->decimal('score', 5, 2);

            $table->integer('player_id')->unsigned()->index();
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->integer('stat_id')->unsigned()->index();
            $table->foreign('stat_id')->references('id')->on('stats')->onDelete('cascade');

            $table->integer('team_id');
            $table->integer('game_id');
            $table->integer('game_set_id');
            $table->integer('point_id');

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
        Schema::dropIfExists('player_stats');
    }
}
