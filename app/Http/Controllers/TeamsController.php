<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\Team;
use App\Models\User;
use App\Models\Game;

class TeamsController extends Controller
{
    public function index() 
    {
        return inertia('Teams', ['teams' => $this->getTeams()]);
    }

    public function store($id = null) 
    {
        request()->validate([
            'name' => 'required',
        ]);

        $team = (new Team)->saveTeam(request()->only(['name']), $id);

        if (request('json')) {
            return response()->json([
                'team' => $team,
            ]);
        } else {
            return redirect()->route('teams.index');
        }
    }

    public function resetTeam() 
    {
        $user = auth()->user();
        $user->current_team_id = null;
        $user->save();
        return redirect()->route('home');
    }

    public function select() 
    {
        request()->validate([
            'team_id' => 'required',
        ]);

        $team = Team::findOrFail( request('team_id'));

        $user = auth()->user();
        $user->current_team_id = $team->id;
        $user->save();

        return redirect()->route('home');

    }

    public function search() 
    {
        return (new Team)->search();
    }

    public function addPlayer($id) 
    {
        request()->validate([
            'user' => 'required',
            'game_id' => 'required',
        ]);

        $input = request()->all();

        $team = Team::findOrFail($id);
        $user = User::findOrFail( Arr::get($input, 'user.id'));

        $team->addUser($user);

        $game = Game::findOrFail( Arr::get($input, 'game_id'));

        return redirect()->route('games.view', ['id' => $game->id]);
    }

    protected function getTeams()
    {
        return Team::all();
    }
}
