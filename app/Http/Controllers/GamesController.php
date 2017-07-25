<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\Game;

use App\Events\GameRemoved;
use App\Events\GamesRefresh;

class GamesController extends Controller
{
    
    protected $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function games()
    {
        return $this->game->where('removed', false)->with('team1', 'team2')->get()
            ->sortBy('team1_id')
            ->sortByDesc('start_time')
            ->values()
            ->all();
    }

    public function create(Requests\GameSave $request) 
    {
        $game = new Game;
        return $game->saveGame($request->only('team1_id', 'team2_id', 'start_time'));
    }

    public function load($id)
    {
        return $this->game->findOrFail($id)->load('team1', 'team2');
    }

    public function store(Requests\GameSave $request, $id)
    {
        return $this->game->findOrFail($id)
            ->saveGame($request->only('team1_id', 'team2_id', 'start_time'));
    }

    public function destroy(Request $request, $id)
    {
        $game = $this->game->findOrFail($id);
        $game->removed = true;
        $game->save();
        event(new GameRemoved($game->id.' has been removed'));
        event(new GamesRefresh());
        return $game;
    }

    public function gameSets($id)
    {
        return $this->game->findOrFail($id)->gameSets;
    }

    public function addPoint(Request $request, $id) 
    {
        return $this->game->findOrFail($id)->addPoint($request->input('team_id'));
    }

    public function removePoint(Request $request, $id) 
    {
        return $this->game->findOrFail($id)->removePoint($request->input('team_id'));
    }

    public function addSet($id)
    {
        return $this->game->findOrFail($id)->addGameSet();
    }
}
