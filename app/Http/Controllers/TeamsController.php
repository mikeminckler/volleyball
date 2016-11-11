<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Team;

use App\Events\TeamRemoved;

class TeamsController extends Controller
{
    
    protected $team;

    public function __construct(Team $team) 
    {
        $this->team = $team;
    }

    public function teams()
    {
        return $this->team->where('hidden', '0')->get()
            ->sortBy('team_name')
            ->values()
            ->all();
    }

    public function create(Requests\TeamSave $request)
    {
        $team = new Team;
        return $this->team
            ->saveTeam($request->only('team_name'));
    }

    public function load($id)
    {
        return $this->team->findOrFail($id);
    }

    public function store(Requests\TeamSave $request, $id)
    {
        return $this->team->findOrFail($id)
            ->saveTeam($request->only('team_name'));
    }

    public function destroy(Request $request, $id)
    {
        $team = $this->team->findOrFail($id);
        $team->hidden = true;
        $team->save();
        event(new TeamRemoved($team->team_name.' has been removed'));
        return $team;
    }
}
