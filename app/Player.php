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

        $team_stat = $team->stats()->where('stat_id', $stat->id)->first();
        $score_high = $team_stat->pivot->score_high;
        $score_low = $team_stat->pivot->score_low;

        $player_stats = $this->stats()->where('stat_id', $stat->id)
                                ->where('game_id', $game->id)
                                ->where('team_id', $team->id)->get();

        if ($player_stats->count()) {

            $attempts = $player_stats->count();

            $total = $player_stats->sum(function($player_stat) {
                return $player_stat->score;
            });

            if ($score_low == $score_high) {

                $score = $attempts;
                $attempts = 0;
            
            } else if ($score_low == -1 && $score_high == 1) {

                $successes = $player_stats->filter(function($player_stat) {
                    if ($player_stat->score == 1) {
                        return true;
                    } else {
                        return false;
                    }
                })->count();

                $errors = $player_stats->filter(function($player_stat) {
                    if ($player_stat->score == -1) {
                        return true;
                    } else {
                        return false;
                    }
                })->count();

                $score = number_format(((($successes - $errors) / $attempts) * 100), 1, '', '');

            } else {
                $score = number_format(round(($total / $attempts), 2), 2, '.', '');
            }

            return ['score' => $score, 'attempts' => $attempts];

        } else {
            return ['score' => 0, 'attempts' => 0];
        }
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
