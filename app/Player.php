<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
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
        return $this->where('hidden', '0')
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
}
