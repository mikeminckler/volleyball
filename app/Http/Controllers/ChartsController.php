<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use App\Team;
use App\Game;
use App\Stat;
use App\Player;
use App\Chart;

class ChartsController extends Controller
{
    
    protected $game;
    protected $team;
    protected $stat;
    protected $player;

    public function __construct(Game $game, Stat $stat, Team $team, Player $player)
    {
        $this->game = $game;
        $this->stat = $stat;
        $this->team = $team;
        $this->player = $player;
    }

    public function playerGames(Request $request) 
    {
        $players = $this->player->whereIn('id', $request->input('players'))->get();
        $games = $this->game->whereIn('id', $request->input('games'))->get();

        return Chart::createData($games, null, $players);

    }

    public function teamGames(Request $request)
    {
        $team = $this->team->findOrFail($request->input('team_id'));
        $games = $this->game->whereIn('id', $request->input('games'))->get();
        if ($request->input('players')) {
            $players = $this->player->whereIn('id', $request->input('players'))->get();
        } else {
            $players = null;
        }

        return Chart::createData($games, $team, $players);


    }
}
