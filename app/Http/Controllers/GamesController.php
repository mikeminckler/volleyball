<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Models\Game;
use App\Models\User;
use App\Utilities\Chart;

use App\Events\GameUpdated;

class GamesController extends Controller
{

    public function store($id = null) 
    {
        request()->validate([
            'team1.id' => 'required',
            'team2.id' => 'required',
        ]);

        $game = (new Game)->saveGame(request()->only(['team1', 'team2', 'notes']), $id);

        broadcast(new GameUpdated($game)); 

        return redirect()->route('games.view', ['id' => $game->id]);
    }

    public function view($id) 
    {
        $game = Game::findOrFail($id);
        $game->load(['team1.users', 'team2']);

        return inertia('Game', ['game' => $game]);
    }

    public function chart() 
    {
        $input = request()->all();

        $games = Game::whereIn('id', collect( Arr::get($input, 'games'))->pluck('id'))->get();
        $players = User::whereIn('id', collect( Arr::get($input, 'players'))->pluck('id'))->get();

        return response()->json([
            'data' => Chart::convertGamesForGoogleChart($games, $players),
        ]);
    }
}
