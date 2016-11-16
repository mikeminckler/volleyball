<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        return $this;
    }

    public function getTeam1PointsAttribute()
    {
        return $this->points->where('team_id', $this->game->team1->id)->count();
    }

    public function getTeam2PointsAttribute()
    {
        return $this->points->where('team_id', $this->game->team2->id)->count();
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
        return $this->team1_points.'-'.$this->team2_points;
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
