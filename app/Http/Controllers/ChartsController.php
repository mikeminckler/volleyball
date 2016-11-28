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

        $chart_data = new Collection;
        $ticks = new Collection;

        $scores = array();
        $team_stats = $team->stats;

        foreach ($team_stats as $team_stat) {
            $scores[$team_stat->id] = 0;
        }

        $total = 0;
        $touch = 0;

        $chart_data->push(collect([$touch, $total, $scores])->flatten());

        foreach ($games as $game) {

            $player_stats = new Collection;

            foreach ($players as $player) {
                $player_stats = $player_stats->merge($player->stats()
                    ->where('game_id', $game->id)
                    ->where('team_id', $team->id)->get()
                );
            }

            $player_stats = $player_stats->sortBy(function($player_stat) {
                return $player_stat->created_at;
            })->values();

            if ($player_stats->count()) {
                $previous_game_set_id = $player_stats->first()->game_set_id;
            }


            $ticks->push(['v' => $touch, 'f' => 'vs '.$game->opposingTeam($team)->team_name]);


            foreach ($player_stats as $player_stat) {

                $scores[$player_stat->stat->id] += $player_stat->chartScore($team);
                $total += $player_stat->chartScore($team);

                $chart_data->push(collect([$touch, $total, $scores])->flatten());

                if ($previous_game_set_id != $player_stat->game_set_id) {
                    $ticks->push(['v' => $touch, 'f' => 'Set '.$player_stat->gameSet->number]);
                    $previous_game_set_id = $player_stat->game_set_id;
                }

                $touch++;
            }
        }

        $chart_headers = collect(['Touch', 'Total '.$total]);

        foreach ($team_stats as $team_stat) {
            $chart_headers->push($team_stat->stat_name.' '.$team->gameStatScore($team_stat, $games));
        }

        $chart = collect([$chart_headers])->merge($chart_data);

        return ['chart' => $chart, 'ticks' => array_values($ticks->toArray())];

    }
}
