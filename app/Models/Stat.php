<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    use HasFactory;

    protected $casts = [
        'high_score' => 'float',
        'low_score' => 'float',
    ];

    public function calculateScore($user_stats) 
    {
        $high_score = $this->high_score;
        $low_score = $this->low_score;

        $attempts = $user_stats->count();

        if (!$attempts) {
            return ['score' => 0, 'attempts' => 0];
        }
         
        $total = $user_stats->sum(function($user_stat) {
            return $user_stat->score;
        });

        if ($low_score == $high_score) {

            $score = $attempts;
            $attempts = 0;
        
        } else if ($low_score == -1 && $high_score == 1) {

            $successes = $user_stats->filter(function($user_stat) {
                if ($user_stat->score == 1) {
                    return true;
                } else {
                    return false;
                }
            })->count();

            $errors = $user_stats->filter(function($user_stat) {
                if ($user_stat->score == -1) {
                    return true;
                } else {
                    return false;
                }
            })->count();

            $score = number_format(((($successes - $errors) / $attempts) * 100), 1, '', '');

        } else {
            $score = number_format(round(($total / $attempts), 2), 2, '.', '');
        }

        $latest = $user_stats->sortByDesc('id')->values()->take(10)->map->chart_score;

        return ['score' => $score, 'attempts' => $attempts, 'latest' => $latest];
    }
}
