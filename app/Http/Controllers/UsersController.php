<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Models\UserStat;
use App\Models\User;
use App\Models\Game;
use App\Models\Stat;

use App\Events\UserStatCreated;
use App\Events\UserStatDeleted;

class UsersController extends Controller
{
    public function search() 
    {
        return (new User)->search();
    }

    public function store($id = null) 
    {
        request()->validate([
            'name' => 'required',
        ]);

        $user = (new User)->saveUser(request()->only(['name', 'nickname', 'email']), $id);

        if (request('json')) {
            return response()->json([
                'user' => $user,
            ]);
        } else {
            return redirect()->route('users.edit', ['id' => $user->id]);
        }
    }

    public function edit($id) 
    {
        $user = User::findOrFail($id);
        return inertia('User', ['user' => $user]);
    }

    public function createStat($id) 
    {
        request()->validate([
            'game' => 'required',
            'stat' => 'required',
            'score' => 'required|numeric',
        ]);

        $input = request()->all();

        $user = User::findOrFail($id);
        $game = Game::findOrFail( Arr::get($input, 'game.id'));
        $stat = Stat::findOrFail( Arr::get($input, 'stat.id'));

        $user_stat = new UserStat;
        $user_stat->user_id = $user->id;
        $user_stat->game_id = $game->id;
        $user_stat->stat_id = $stat->id;
        $user_stat->point = $game->stats->count();
        $user_stat->score = (float) Arr::get($input, 'score');
        $user_stat->save();

        broadcast(new UserStatCreated($user_stat)); 

        return response()->json([
            'score' => $user->getScore($stat, collect([$game])),
        ]);
    }

    public function undoStat($id) 
    {
        request()->validate([
            'game' => 'required',
            'stat' => 'required',
        ]);

        $input = request()->all();

        $user = User::findOrFail($id);
        $game = Game::findOrFail( Arr::get($input, 'game.id'));
        $stat = Stat::findOrFail( Arr::get($input, 'stat.id'));

        $user_stat = UserStat::where('user_id', $user->id)
            ->where('stat_id', $stat->id)
            ->where('game_id', $game->id)
            ->latest()
            ->first();

        if ($user_stat) {
            $user_stat->delete();
            broadcast(new UserStatDeleted($game, $stat, $user)); 
        }

        return response()->json([
            'score' => $user->getScore($stat, collect([$game])),
        ]);
    }


    public function statScore($id) 
    {
        request()->validate([
            'games' => 'required',
            'stat' => 'required',
        ]);

        $input = request()->all();
        $user = User::findOrFail($id);   

        $game_ids = collect( Arr::get($input, 'games'))->map(function($game) {
            return $game['id'];
        })->toArray();

        $games = Game::whereIn('id', $game_ids)->get();
        $stat = Stat::findOrFail( Arr::get($input, 'stat.id'));

        return response()->json([
            'score' => $user->getScore($stat, $games),
        ]);
    }
}
