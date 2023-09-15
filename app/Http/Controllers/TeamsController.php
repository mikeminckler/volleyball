<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\Team;
use App\Models\User;
use App\Models\Game;
use App\Models\Stat;

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
        ]);

        $input = request()->all();
        $team = Team::findOrFail($id);
        $user = User::findOrFail( Arr::get($input, 'user.id'));

        $team->addUser($user);

        return redirect()->route('home');
    }

    public function removePlayer($id) 
    {
        request()->validate([
            'user' => 'required',
        ]);

        $input = request()->all();
        $team = Team::findOrFail($id);
        $user = User::findOrFail( Arr::get($input, 'user.id'));

        $team->removeUser($user);

        return redirect()->route('home');
    }

    public function sortPlayer($id) 
    {
        request()->validate([
            'user' => 'required',
            'direction' => 'required',
        ]);

        $input = request()->all();
        $team = Team::findOrFail($id);
        $user = $team->users->firstWhere('id', Arr::get($input, 'user.id'));

        $users = $team->users->sortBy(function($player) use($user, $input) {
            if ($player->id === $user->id) {
            
                if ( Arr::get($input, 'direction') === 'up') {
                    return (int) $player->pivot->sort_order - 1.5;
                } else {
                    return (int) $player->pivot->sort_order + 1.5;
                }

            }
            return (int) $player->pivot->sort_order;
        })
        ->values()
        ->each(function($player, $new_order) use($team) {
            $team->users()->updateExistingPivot($player->id, ['sort_order' => $new_order + 1]);
        });

        return redirect()->route('home');
    }

    public function statScore($id) 
    {
        request()->validate([
            'games' => 'required',
            'stat' => 'required',
        ]);

        $input = request()->all();
        $team = Team::findOrFail($id);   

        $game_ids = collect( Arr::get($input, 'games'))->map(function($game) {
            return $game['id'];
        })->toArray();

        $games = Game::whereIn('id', $game_ids)->get();
        $stat = Stat::findOrFail( Arr::get($input, 'stat.id'));

        return response()->json([
            'score' => $team->getScore($stat, $games),
        ]);
    }

    protected function getTeams()
    {
        return Team::all();
    }
}
