<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

use App\Models\Team;
use App\Models\UserStat;

class Game extends Model
{
    use HasFactory;

    protected $visible = ['id', 'team1', 'team2', 'created_at'];

    public function saveGame($input, $id = null) 
    {
        if ($id) {
            $game = Game::findOrFail($id);
        } else {
            $game = new Game();
        }

        $team1 = Team::findOrFail( Arr::get($input, 'team1.id'));
        $team2 = Team::findOrFail( Arr::get($input, 'team2.id'));

        $game->team1_id = $team1->id;
        $game->team2_id = $team2->id;
        $game->save();

        return $game;
    }

    public function team1() 
    {
        return $this->belongsTo(Team::class);
    }

    public function team2() 
    {
        return $this->belongsTo(Team::class);
    }

    public function stats() 
    {
        return $this->hasMany(UserStat::class);   
    }
}
