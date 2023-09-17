<?php

namespace App\Utilities;

use Illuminate\Support\Collection;

use App\Models\Stat;
use App\Models\UserStat;

class Chart
{

    public static function convertGamesForGoogleChart($games, $players = null) 
    {

        if (!$games instanceof Collection) {
            $games = collect([$games]);
        }

        $headers = collect(['Touch', 'Total']);
        $data = collect();
        $scores = array();
        $total = 0;
        $touch = 0;

        foreach (Stat::all() as $stat) {
            $headers->push($stat->name);
            $scores[$stat->id] = 0;
        }

        if ($players->count()) {
            foreach ($players as $player) {
                $headers->push($player->nickname ?? $player->name);
                $scores['p-'.$player->id] = 0;
            }
        }
        
        // setup the first row
        $data->push(collect([$touch, $total, $scores])->flatten());

        $user_stats = UserStat::whereIn('game_id', $games->pluck('id'))->get();

        $player_stats = UserStat::whereIn('user_id', $players->pluck('id'))
            ->whereIn('game_id', $games->pluck('id'))->get();

        foreach ($user_stats as $stat) {

            $touch++;

            $scores[$stat->stat_id] += $stat->chart_score;
            $total += $stat->chart_score;

            $player_stat = $player_stats->firstWhere('id', $stat->id);

            if ($player_stat) {
                $scores['p-'.$stat->user_id] += $stat->chart_score;
            }

            $data->push(collect([$touch, $total, $scores])->flatten());
        }

        return collect([$headers])->merge($data);

    }

}
