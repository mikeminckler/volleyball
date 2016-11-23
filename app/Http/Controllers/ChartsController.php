<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use App\Team;
use App\Game;
use App\Stat;

class ChartsController extends Controller
{
    
    protected $game;
    protected $team;
    protected $stat;

    public function __construct(Game $game, Stat $stat, Team $team)
    {
        $this->game = $game;
        $this->stat = $stat;
        $this->team = $team;
    }

    public function teamGames(Request $request)
    {
        $team = $this->team->findOrFail($request->input('team_id'));
        $games = $this->game->whereIn('id', $request->input('games'))->get();
        $players = $team->players;

        $player_stats = new Collection;

        foreach ($games as $game) {
            foreach ($players as $player) {
                $player_stats = $player_stats->merge($player->stats()
                    ->where('game_id', $game->id)
                    ->where('team_id', $team->id)->get()
                );
            }
        }

        $player_stats = $player_stats->sortBy(function($player_stat) {
            return $player_stat->created_at;
        })->values();

        $chart_headers = collect(['Touch']);

        $team_stats = $team->stats;

        $scores = array();

        foreach ($team_stats as $team_stat) {
            $chart_headers->push($team_stat->stat_name);
            $scores[$team_stat->id] = 0;
        }

        $chart_data = new Collection;
        $chart_data->push($chart_headers);

        $chart_data->push(collect([0, $scores])->flatten());

        foreach ($player_stats as $key => $player_stat) {

            $scores[$player_stat->stat->id] += $player_stat->chartScore($team);

            $chart_data->push(collect([($key + 1), $scores])->flatten());
        }

        return $chart_data;

    }
}
