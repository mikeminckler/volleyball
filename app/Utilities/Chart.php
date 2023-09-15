<?php

namespace App\Utilities;

use Illuminate\Support\Collection;

use App\Models\Stat;
use App\Models\UserStat;

class Chart
{

    public static function convertGamesForGoogleChart($games) 
    {

        if (!$games instanceof Collection) {
            $games = collect([$games]);
        }

        $user_stats = UserStat::whereIn('game_id', $games->pluck('id'))->get();
        
        $headers = collect(['Touch', 'Total']);
        $data = collect();
        $scores = array();
        $total = 0;
        $touch = 0;

        foreach (Stat::all() as $stat) {
            $headers->push($stat->name);
            $scores[$stat->id] = 0;
        }

        $data->push(collect([$touch, $total, $scores])->flatten());

        foreach ($user_stats as $stat) {
            $touch++;

            $scores[$stat->stat_id] += $stat->chart_score;
            $total += $stat->chart_score;

            $data->push(collect([$touch, $total, $scores])->flatten());
        }

        return collect([$headers])->merge($data);

    }

}
