<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Game;
use App\Utilities\Chart;

class GamesController extends Controller
{

    public function store($id = null) 
    {
        request()->validate([
            'team1.id' => 'required',
            'team2.id' => 'required',
        ]);

        $game = (new Game)->saveGame(request()->only(['team1', 'team2']), $id);

        return redirect()->route('games.view', ['id' => $game->id]);
    }

    public function view($id) 
    {
        $game = Game::findOrFail($id);
        $game->load(['team1.users', 'team2']);

        return inertia('Game', ['game' => $game]);
    }

    public function chart($id) 
    {
        $game = Game::findOrFail($id);

        return response()->json([
            'data' => Chart::convertGamesForGoogleChart($game),
        ]);
    }
}
