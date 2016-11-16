<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Player;

use App\Events\TeamsRefresh;
use App\Events\TeamCreated;
use App\Events\TeamUpdated;

class Team extends Model
{
    public function saveTeam($input)
    {
    
        if (!$this->id) {
            $created = true;
        } else {
            $created = false;
        }

        $this->team_name = $input['team_name'];
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

            $user->addRole('player');
        }

        if (!$this->players->contains($player)) {
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

}
