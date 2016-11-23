<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Player;
use App\Game;
use App\Stat;
use App\Team;

use App\Events\PlayerGameStatsUpdated;

class PlayerStat extends Model
{

    public function chartScore($team)
    {

        if (!$team instanceof Team) {
            $team = Team::findOrFail($team);
        }

        $team_stat = $team->stats()->where('stat_id', $this->stat->id)->first();
        $score_high = $team_stat->pivot->score_high;
        $score_low = $team_stat->pivot->score_low;
        
        if ($score_low == $score_high) {

            return $this->score;

        } else if ($score_low == -1 && $score_high == 1) {

            return $this->score;

        } else if ($score_low > $score_high) {

            if ($this->score > 0) {
                $average = $this->score / $score_low;
                $diff = 1 - $average;
            } else {
                $diff = 1;
            }

            return round((($diff - 0.5) * 2), 2);

        } else {

            if ($this->score > 0) {
                $average =  $this->score / $score_high;
            } else {
                $average = 0;
            }

            return round(($average - 0.5) * 2, 2);

        }

    }

    public static function createPlayerStat(Player $player, Game $game, Stat $stat, Team $team, $score)
    {
        $player_stat = new PlayerStat;

        $player_stat->player_id = $player->id;
        $player_stat->stat_id = $stat->id;
        $player_stat->team_id = $team->id;
        $player_stat->game_id = $game->id;
        $player_stat->game_set_id = $game->currentSet()->id;
        $player_stat->point_id = $game->currentPoint()->id;
        $player_stat->score = $score;

        $player_stat->save();

        event( new PlayerGameStatsUpdated($player, $game, $stat) );

        return $player_stat;

    }

    public function player()
    {
        return $this->belongsTo('App\Player');
    }

    public function stat()
    {
        return $this->belongsTo('App\Stat');
    }

    public function team()
    {
        return $this->belongsTo('App\Team');
    }
    
    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    public function gameSet()
    {
        return $this->belongsTo('App\GameSet');
    }

    public function point()
    {
        return $this->belongsTo('App\Point');
    }


}
