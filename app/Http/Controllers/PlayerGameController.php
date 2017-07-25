<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Player;
use App\Game;
use App\Stat;
use App\Team;

class PlayerGameController extends Controller
{
    protected $player;
    protected $game;
    protected $stat;

    public function __construct(Player $player, Game $game, Stat $stat, Team $team)
    {
        $this->player = $player;
        $this->game = $game;
        $this->stat = $stat;
        $this->team = $team;
    }

    public function getStatScore(Request $request, $id)
    {
        $player = $this->player->findOrFail($id);
        $stat = $this->stat->findOrFail($request->input('stat_id'));
        $game = $this->game->findOrFail($request->input('game_id'));
        $team = $this->team->findOrFail($request->input('team_id'));

        return $player->getGameStatScore($game, $stat, $team);
    }

    public function addStatScore(Request $request, $id)
    {
        $player = $this->player->findOrFail($id);
        $stat = $this->stat->findOrFail($request->input('stat_id'));
        $game = $this->game->findOrFail($request->input('game_id'));
        $team = $this->team->findOrFail($request->input('team_id'));
        $score = $request->input('score');

        $player->addGameStatScore($game, $stat, $team, $score);

        return $player->getGameStatScore($game, $stat, $team);
    }

}
