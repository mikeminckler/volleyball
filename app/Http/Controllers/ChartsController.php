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

        $chart_headers = collect(['Touch', 'Total']);

        $team_stats = $team->stats;

        $scores = array();

        foreach ($team_stats as $team_stat) {
            $chart_headers->push($team_stat->stat_name);
            $scores[$team_stat->id] = 0;
        }

        $chart_data = new Collection;
        $total = 0;
        $set_count = 1;

        if ($player_stats->count()) {
            $previous_game_set_id = $player_stats->first()->game_set_id;
        }

        $chart_data->push($chart_headers);
        $chart_data->push(collect([0, $total, $scores])->flatten());


        $ticks = new Collection;
        $ticks->push(['v' => '0', 'f' => 'vs '.$game->opposingTeam($team)->team_name]);

        foreach ($player_stats as $key => $player_stat) {

            $scores[$player_stat->stat->id] += $player_stat->chartScore($team);
            $total += $player_stat->chartScore($team);

            $chart_data->push(collect([($key + 1), $total, $scores])->flatten());

            if ($previous_game_set_id != $player_stat->game_set_id) {
                $set_count ++;
                $ticks->push(['v' => $key, 'f' => 'Set '.$set_count]);
                $previous_game_set_id = $player_stat->game_set_id;
            }

        }

        return ['chart' => $chart_data, 'ticks' => array_values($ticks->toArray())];

    }
}
