<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Player;
use App\PlayerStat;
use App\Game;
use App\Stat;
use App\Team;

use App\Events\PlayerGameStatsUpdated;

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

    public function removeLastStat($id)
    {
        $player = $this->player->findOrFail($id);
        $stat = $player->stats->sortByDesc('created_at')->first();
        if ($stat instanceof PlayerStat) {
            $game = $stat->getGame();
            $statType = $stat->stat;
            $stat->delete();
            if ($game instanceof Game) {
                event( new PlayerGameStatsUpdated($player, $game, $statType) );
            } else {
                return response()->json(['error' => 'Could not find Game'], 422);
            }
            return response()->json(['success' => 'Removed stat for '.$player->full_name]);
        } else {
            return response()->json(['error' => 'Could not find a stat for '.$player->full_name], 422);
        }

    }

}
