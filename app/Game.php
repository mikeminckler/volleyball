<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Cache;

use App\GameSet;
use App\Point;
use App\PlayerStat;
use App\Team;

use App\Events\GameCreated;
use App\Events\GamesRefresh;
use App\Events\GameUpdated;

class Game extends Model
{

    protected $appends = ['score', 'team1_name', 'team2_name'];

    public function team1() 
    {
        return $this->hasOne(Team::class, 'id', 'team1_id');
    }

    public function team2() 
    {
        return $this->hasOne(Team::class, 'id', 'team2_id');
    }

    public function gameSets() 
    {
        return $this->hasMany(GameSet::class);
    }


    public function saveGame($input)
    {
    
        if (!$this->id) {
            $created = true;
        } else {
            $created = false;
        }

        $this->team1_id = $input['team1_id'];
        $this->team2_id = $input['team2_id'];
        $this->start_time = $input['start_time'];
        $this->save();

        if ($created) {

            /**
             * Let's create the first set and point 
             */
            
            $this->addGameSet();
            $this->addPoint();

            event(new GameCreated($this->team1->team_name.' vs '.$this->team2->team_name.' been created', '/games/stats/'.$this->id));
        }

        event(new GamesRefresh());

        Cache::tags(['game_'.$this->id])->flush();

        return $this;
    }

    public function addGameSet()
    {
        $game_set = new GameSet;
        $game_set->game_id = $this->id;
        $game_set->save();

        event(new GamesRefresh());
        event(new GameUpdated($this, 'Set '.$game_set->number.' has been created'));

        Cache::tags(['game_'.$this->id])->flush();

        return $game_set;
    }

    public function getScoreAttribute()
    {
        $score = Cache::tags(['game_'.$this->id])->rememberForever('game_'.$this->id.'_score', function() {
            return $this->gameSets()->get()->implode('full_score', ', ');
        });
        return $score;
    }

    public function getTeam1NameAttribute()
    {
        $name = Cache::tags(['game_'.$this->id])->rememberForever('game_'.$this->id.'_team1', function() {
            return $this->team1()->get()->first()->team_name;
        });
        return $name;
    }

    public function getTeam2NameAttribute()
    {
        $name = Cache::tags(['game_'.$this->id])->rememberForever('game_'.$this->id.'_team2', function() {
            return $this->team2()->get()->first()->team_name;
        });
        return $name;
    }

    public function addPoint($team = null)
    {

        $game_set = $this->currentSet();
        if ($team) {
            $team = Team::findOrFail($team);
        }

        $point = new Point;
        if ($team instanceof Team) {
            $point->team_id = $team->id;
        }
        $point->game_set_id = $game_set->id;
        $point->save();

        if ($team instanceof Team) {
            event(new GamesRefresh());
            event(new GameUpdated($this, $team->team_name.' has scored a point'));
        }

        Cache::tags(['game_'.$this->id])->flush();

        return $this;

    }

    public function removePoint($team_id)
    {
        $game_set = $this->currentSet();
        $team = Team::findOrFail($team_id);

        $point = $game_set->points->sortByDesc('created_at')->values()->filter(function($point) use($team_id) {
            return $point->team_id == $team_id;
        })->last();
        $point->removed = true;
        $point->save();

        event(new GamesRefresh());
        event(new GameUpdated($this, $team->team_name.' score corrected'));

        Cache::tags(['game_'.$this->id])->flush();

        return $this;
    }

    public function currentSet()
    {
        return $this->gameSets->sortByDesc('created_at')->values()->first();
    }

    public function currentPoint()
    {
        return $this->points->sortByDesc('created_at')->values()->first();
    }

    public function points() 
    {
        return $this->hasManyThrough(Point::class, GameSet::class)->orderBy('created_at', 'desc');
    }

    public function opposingTeam($team) {
        if ($this->team1->id != $team->id) {
            return $this->team1;
        } else {
            return $this->team2;
        }
    }

    public function playerStats()
    {
        return $this->hasMany(PlayerStat::class);
    }

}
