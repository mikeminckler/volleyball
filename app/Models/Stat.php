<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    use HasFactory;

    protected $appends = ['reverse'];

    protected $casts = [
        'high_score' => 'float',
        'low_score' => 'float',
    ];

    public function getReverseAttribute() 
    {
        return $this->high_score < $this->low_score;
    }

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

        if ($this->name === 'Blocking') {

            $score = $total;
        
        } else if ($this->name === 'Hitting') {

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

        $latest = $user_stats->sortByDesc('id')->values()->take(20)->map->chart_score;

        $totals = $user_stats->groupBy('score')->map(function($items, $value) {
            return [
                'score' => (int) $value, 
                'chart_score' => $items->first()->chart_score,
                'total' => $items->count()
            ];
        })->sortKeys()->values();

        return ['score' => $score, 'attempts' => $attempts, 'latest' => $latest, 'totals' => $totals];
    }
}
