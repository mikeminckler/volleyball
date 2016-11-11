<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Events\TeamsRefresh;
use App\Events\TeamCreated;

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
        return $this->where('hidden', '0')
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
}
