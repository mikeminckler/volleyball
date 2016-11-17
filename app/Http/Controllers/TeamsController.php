<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Team;
use App\Player;
use App\User;;

use App\Events\TeamRemoved;
use App\Events\TeamsRefresh;

class TeamsController extends Controller
{
    
    protected $team;
    protected $player;
    protected $user;

    public function __construct(Team $team, Player $player, User $user) 
    {
        $this->team = $team;
        $this->player = $player;
        $this->user = $user;
    }

    public function teams()
    {
        return $this->team->where('removed', false)->get()
            ->sortBy('team_name')
            ->values()
            ->all();
    }

    public function create(Requests\TeamSave $request)
    {
        $team = new Team;
        return $team
            ->saveTeam($request->only('team_name'));
    }

    public function load($id)
    {
        return $this->team->findOrFail($id)->load('players');
    }

    public function store(Requests\TeamSave $request, $id)
    {
        return $this->team->findOrFail($id)
            ->saveTeam($request->only('team_name'));
    }

    public function destroy(Request $request, $id)
    {
        $team = $this->team->findOrFail($id);
        $team->removed = true;
        $team->save();
        event(new TeamRemoved($team->team_name.' has been removed'));
        event(new TeamsRefresh());
        return $team;
    }

    public function players($id) 
    {
        return $this->team->findOrFail($id)->players->sortBy(function($player) {
            return $player->user->last_name;
        })->values()->all();
    }

    /**
     * We pass in the user id here instead of the player id
     * which allows us to create a player object if we 
     * need to. 
     */

    public function addPlayer(Request $request, $id)
    {
        $user = $this->user->findOrFail($request->input('user_id'));
        return $this->team->findOrFail($id)->addPlayer($user);
    }

    public function removePlayer(Request $request, $id)
    {
        $player = $this->player->findOrFail($request->input('player_id'));
        return $this->team->findOrFail($id)->removePlayer($player);

    }
}
