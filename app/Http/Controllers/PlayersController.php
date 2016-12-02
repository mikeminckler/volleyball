<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Player;
use App\Game;

class PlayersController extends Controller
{
    protected $player;
    protected $game;

    public function __construct(Player $player, Game $game) 
    {
        $this->player = $player;
        $this->game = $game;
    }

    public function load($id)
    {
        return $this->player->findOrFail($id)->load('teams');
    }

    public function teams($id)
    {
        return $this->player->findOrFail($id)->teams;
    }

    public function games($id)
    {
        return $this->player->findOrFail($id)->games();
    }

    public function gameReport(Request $request, $id)
    {
        $games = $this->game->whereIn('id', $request->input('game_ids'))->get();
        return $this->player->findOrFail($id)->gamesReport($games);
    }

}
