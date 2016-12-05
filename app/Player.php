<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use App\Game;
use App\Stat;
use App\Team;
use App\PlayerStat;

use App\Events\TeamGameChartUpdated;

class Player extends Model
{
    use UserAttributes;

    protected $appends = ['games', 'full_name'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function teams()
    {
        return $this->belongsToMany('App\Team')->withPivot('number');
    }

    public function games()
    {
        $games = new Collection;
        $teams = $this->teams;
        foreach ($teams as $team) {
            $games = $games->merge($team->games());
        }
        return $games->unique();
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
            $player_array['selected'] = false;
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

    public function getGamesAttribute()
    {
        return $this->games();
    }

    public function playedForTeamInGame($game)
    {
        $teams = $this->teams;

        if ($teams->contains($game->team1->id)) {
            return $game->team1;
        }    

        if ($teams->contains($game->team2->id)) {
            return $game->team2;
        }    

    }

    public function gamesReport($games)
    {

        $report = new Collection;

        $team_check = $this->stats()
                        ->whereIn('game_id', $games->pluck('id'))
                        ->get()->groupBy('team_id');

        foreach ($team_check as $team_id => $team_stats) {

            $team = Team::findOrFail($team_id); 

            foreach ($games as $game) {

                $all_stats = $this->stats()
                    ->where('game_id', $game->id)
                    ->where('team_id', $team->id)
                    ->orderBy('player_stats.created_at')
                    ->get();

                $report_info = array();
                $report_info['stats'] = array_values(Stat::statsReport($all_stats, $team));
                $report_info['versus_team'] = 'vs '.$game->opposingTeam($team)->team_name;
                $report->push($report_info);

            }

            $team_info = array();
            $team_info['stats'] = array_values(Stat::statsReport($team_stats, $team));
            $team_info['versus_team'] = 'Total for '.$team->team_name;
            $report->push($team_info);

        }

        return $report;

    }

}
