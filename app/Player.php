<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Game;
use App\Stat;
use App\Team;
use App\PlayerStat;

use App\Events\TeamGameChartUpdated;

class Player extends Model
{
    use UserAttributes;

    protected $appends = ['full_name'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function teams()
    {
        return $this->belongsToMany('App\Team')->withPivot('number');
    }

    public function search($term)
    {
        return $this->where('removed', false)
            ->where(function($query) use($term) {

                $query->whereHas('user', function($query) use($term) {
                    $query->where('first_name', 'like', '%'.$term.'%')
                        ->orWhere('last_name', 'like', '%'.$term.'%');
                });

            })->get();
    }

    public function searchResultsArray($objects)
    {
        $players = array();
        foreach ($objects as $player) {
            $player_array = array();
            $player_array['id'] = $player->id;
            $player_array['value'] = $player->user->full_name;
            $player_array['label'] = $player->user->full_name;
            $players[] = $player_array;
        }

        return $players;
    }

    public function stats()
    {
        return $this->hasMany('App\PlayerStat');
    }

    public function getGameStatScore($game, $stat, $team)
    {
        if (!$game instanceof Game) {
            $game = Game::findOrFail($game);    
        }

        if (!$stat instanceof Stat) {
            $stat = Stat::findOrFail($stat);    
        }
        
        if (!$team instanceof Team) {
            $team = Team::findOrFail($team);    
        }


        $player_stats = $this->stats()->where('stat_id', $stat->id)
                                ->where('game_id', $game->id)
                                ->where('team_id', $team->id)->get();

        return $team->statAverage($player_stats, $stat);
        
    }

    public function addGameStatScore($game, $stat, $team, $score)
    {
        if (!$game instanceof Game) {
            $game = Game::findOrFail($game);    
        }

        if (!$stat instanceof Stat) {
            $stat = Stat::findOrFail($stat);    
        }

        if (!$team instanceof Team) {
            $team = Team::findOrFail($team);    
        }

        $player_stat = PlayerStat::createPlayerStat($this, $game, $stat, $team, $score);

        event(new TeamGameChartUpdated($player_stat));

        return $this;

    }

}
