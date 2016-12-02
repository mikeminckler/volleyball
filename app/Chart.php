<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use App\Team;
use App\Player;
use App\Game;

class Chart extends Model
{

    public static function createData($games, $team = null, $players = null)
    {

        $total = 0;
        $touch = 0;
        $chart_data = new Collection;
        $ticks = new Collection;
        $scores = array();

        if (!$players && $team instanceof Team) {
            $players = $team->players;
            $teams = Team::where('id', $team->id)->get();
        }

        if (!$team && $players->count()) {
            $team_check = PlayerStat::whereIn('player_id', $players->pluck('id'))
                        ->whereIn('game_id', $games->pluck('id'))
                        ->get()->groupBy('team_id');

            $team_ids = $team_check->keys()->all();
            $teams = Team::whereIn('id', $team_ids)->get();
        }

        foreach ($teams as $team) {

            $team_stats = $team->stats;

            foreach ($team_stats as $team_stat) {
                $scores[$team_stat->id] = 0;
            }
        
        }

        $chart_data->push(collect([$touch, $total, $scores])->flatten());

        foreach ($games as $game) {

            $player_stats = new Collection;


            foreach ($players as $player) {

                $team = $player->playedForTeamInGame($game);

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

                $touch++;

                $scores[$player_stat->stat->id] += $player_stat->chartScore($team);
                $total += $player_stat->chartScore($team);

                $chart_data->push(collect([$touch, $total, $scores])->flatten());

                if ($previous_game_set_id != $player_stat->game_set_id) {
                    $ticks->push(['v' => $touch, 'f' => 'Set '.$player_stat->gameSet->number]);
                    $previous_game_set_id = $player_stat->game_set_id;
                }

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
