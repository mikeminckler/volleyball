<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Cache;

use App\Point;

class GameSet extends Model
{
    protected $appends = ['score'];

    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    public function points()
    {
        return $this->hasMany('App\Point')->where('removed', false);
    }

    public function addPoint($team_id)
    {
        $point = new Point;
        $point->team_id = $team_id;
        $point->game_set_id = $this->id;
        $point->save();

        Cache::tags(['game_'.$this->game->id])->flush();

        return $this;
    }

    public function getTeam1PointsAttribute()
    {
        $points = Cache::tags(['game_'.$this->game->id])->rememberForever('game_'.$this->game->id.'_team1_points', function() {
            return $this->points->where('team_id', $this->game->team1->id)->count();
        });
        return $points;
    }

    public function getTeam2PointsAttribute()
    {
        $points = Cache::tags(['game_'.$this->game->id])->rememberForever('game_'.$this->game->id.'_team2_points', function() {
            return $this->points->where('team_id', $this->game->team2->id)->count();
        });
        return $points;
    }

    public function getScoreAttribute()
    {
        $score = [
            'team1' => [
                'score' => $this->team1_points,
                'id' => $this->game->team1->id
            ],
            'team2' => [
                'score' => $this->team2_points,
                'id' => $this->game->team2->id
            ]
        ];

        return $score;
        
    }

    public function getFullScoreAttribute()
    {
        $score = Cache::tags(['game_'.$this->game->id])->rememberForever('game_'.$this->game->id.'_full_score', function() {
            return $this->team1_points.'-'.$this->team2_points;
        });
        return $score;
    }


    public function getNumberAttribute()
    {
        $number = null;
        foreach ($this->game->gameSets()->get()->sortBy('created_at')->values() as $key => $game_set) {
            if ($game_set->id == $this->id) {
                $number = $key;
            }
        }
        return $number + 1;
    }
}
