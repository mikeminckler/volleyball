<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use App\Player;
use App\Game;

use App\Events\TeamsRefresh;
use App\Events\TeamCreated;
use App\Events\TeamUpdated;

class Team extends Model
{

    protected $appends = ['games', 'stats'];

    public function saveTeam($input)
    {
    
        if (!$this->id) {
            $created = true;
        } else {
            $created = false;
        }

        $this->team_name = $input['team_name'];
        if ($input['initials']) {
            $this->initials = $input['initials'];
        } else {
            $words = preg_split("/[\s,_-]+/", $input['team_name']);
            $acronym = "";

            foreach ($words as $w) {
                  $acronym .= $w[0];
            }
            $this->initials = $acronym;
        }
        $this->save();

        if ($created) {
            event(new TeamCreated($this->team_name.' has been created'));
        }

        event(new TeamsRefresh());

        return $this;
    }

    public function search($term)
    {
        return $this->where('removed', false)
            ->where(function($query) use($term) {
                $query->where('team_name', 'like', '%'.$term.'%');
            })->get();
    }

    public function searchResultsArray($objects)
    {
        $teams = array();
        foreach ($objects as $team) {
                $team_array = array();
                $team_array['id'] = $team->id;
                $team_array['value'] = $team->team_name;
                $team_array['label'] = $team->team_name;
                $team_array['selected'] = false;
                $teams[] = $team_array;
        }

        return $teams;
    }

    public function players()
    {
        return $this->belongsToMany('App\Player')->with('user')->withPivot('number');
    }

    public function addPlayer($user)
    {

        if (!$user instanceof User) {
            $user = User::findOrFail($user);
        }

        if ($user->player instanceof Player) {
            $player = $user->player;
        } else {
            $player = new Player;
            $player->user_id = $user->id;
            $player->save();

            $user->addRole('player', $this);
        }

        $players = $this->players;
        if (!$players->contains($player)) {
            $this->players()->attach($player);
        }

        event(new TeamUpdated($this, 'Added player '.$player->full_name));

        return $this;
    }

    public function removePlayer($player) {

        if (!$player instanceof Player) {
            $player = Player::findOrFail($player);
        }

        $this->players()->detach($player);

        event(new TeamUpdated($this, 'Removed player '.$player->full_name));
        
        return $this;

    }

    public function stats()
    {
        return $this->belongsToMany('App\Stat')->withPivot('score_high', 'score_low', 'target_high', 'target_mid', 'target_low');
    }

    public function playerStats()
    {
        return $this->hasMany('App\PlayerStat');
    }

    public function gameStatScore(Stat $stat, $games)
    {

        if (!$games instanceof Collection) {
            if ($games instanceof Game) {
                $games = collect($games);
            } else {
                $games = Game::findOrFail($games);
                $games = collect($games);
            }
        }

        $team_stats = $this->playerStats()
            ->where('team_id', $this->id)
            ->whereIn('game_id', $games->pluck('id'))
            ->where('stat_id', $stat->id)
            ->get();

        return $this->statAverage($team_stats, $stat)['score'];

    }

    public function homeGames()
    {
        return $this->hasMany('App\Game', 'team1_id');
    }

    public function awayGames()
    {
        return $this->hasMany('App\Game', 'team2_id');
    }

    public function games()
    {
        $home_games = $this->homeGames;
        $away_games = $this->awayGames;
        return $home_games->merge($away_games)->sortByDesc('start_time')->values();
    }

    public function getStatsAttribute()
    {
        return $this->stats()->get();
    }

    public function getGamesAttribute()
    {
        return $this->games();
    }

    public function statAverage($player_stats, Stat $stat)
    {

        $team_stat = $this->stats()->where('stat_id', $stat->id)->first();
        $score_high = $team_stat->pivot->score_high;
        $score_low = $team_stat->pivot->score_low;

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

    public function gameReport($games)
    {

        $report = new Collection;

        foreach ($this->stats as $stat) {

            $stats = $this->playerStats()
                            ->whereIn('game_id', $games->pluck('id'))
                            ->where('stat_id', $stat->id)->get();

            $score = $this->statAverage($stats, $stat);

            $team_stat = $this->stats()->where('stat_id', $stat->id)->first();
            $score_high = $team_stat->pivot->score_high;
            $score_low = $team_stat->pivot->score_low;

            $highs = $stats->filter(function($stat) use($score_high) {
                if ($stat->score == $score_high) {
                    return true;
                }
                return false;
            });

            $lows = $stats->filter(function($stat) use($score_low) {
                if ($stat->score == $score_low) {
                    return true;
                }
                return false;
            });

            $info = [
                'name' => $stat->stat_name,
                'score' => $score,
                'highs' => $highs->count(),
                'lows' => $lows->count()
            ];

            $report->push($info);

        }

        return $report;
    
    }

    public function playersReport($games, $players = null)
    {
        $report = new Collection;

        if (!$players) {
            $players = $this->players;
        }

        foreach ($players as $player) {

            $player_info = $player->toArray();

            foreach ($this->stats as $stat) {

                $stats = $player->stats()
                        ->whereIn('game_id', $games->pluck('id'))
                        ->where('stat_id', $stat->id)
                        ->where('team_id', $this->id)
                        ->get();

                $score = $this->statAverage($stats, $stat);

                $player_info['stats'][] = [
                    'name' => $stat->stat_name,
                    'score' => $score,
                ];

            }

            $report->push($player_info);

        }

        $player_info = ['full_name' => 'Total', 'id' => 0];

        foreach ($this->stats as $stat) {

            $stats = $this->playerStats()
                    ->whereIn('game_id', $games->pluck('id'))
                    ->where('stat_id', $stat->id)
                    ->get();

            $score = $this->statAverage($stats, $stat);

            $player_info['stats'][] = [
                'name' => $stat->stat_name,
                'score' => $score,
            ];

        }

        $report->push($player_info);

        return $report;
    
    }

}
