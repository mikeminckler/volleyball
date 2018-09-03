<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\GameSet;
use App\Game;

class DropExtraFieldsInPlayerStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_stats', function (Blueprint $table) {
            $table->dropColumn('game_set_id');
            $table->dropColumn('game_id');
        });


        GameSet::whereDoesntHave('points')->delete();

        $games = [
            2 => 1,
            4 => 3,
            5 => 3,
            7 => 6,
        ];

        foreach ($games as $old_game_id => $new_game_id) {

            $old_game = Game::find($old_game_id);
            $new_game = Game::find($new_game_id);

            if ($old_game instanceof Game) {

                $sets = $old_game->gameSets;

                foreach ($sets as $set) {
                    $set->game_id = $new_game->id;
                    $set->save();
                }

                $old_game->delete();

            }
        
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('player_stats', function (Blueprint $table) {
            //
        });
    }
}
