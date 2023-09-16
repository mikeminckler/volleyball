<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Stat;

class UserStat extends Model
{
    use HasFactory;

    protected $casts = [
        'score' => 'float',
    ];

    public function stat() 
    {
        return $this->belongsTo(Stat::class);   
    }

    public function getChartScoreAttribute()
    {
        //return cache()->tags(['user-stats'])->rememberForever('user-stat-'.$this->id.'-chart-score', function() {
            $high_score = $this->stat->high_score;
            $low_score = $this->stat->low_score;
            
            if ($low_score == $high_score) {

                return $this->score;

            } else if ($low_score == -1 && $high_score == 1) {

                return $this->score;

            } else if ($low_score > $high_score) {

                if ($this->score > 0) {
                    $average = $this->score / $low_score;
                    $diff = 1 - $average;
                } else {
                    $diff = 1;
                }

                return round((($diff - 0.5) * 2), 2);

            } else {

                if ($this->score > 0) {
                    $average =  $this->score / $high_score;
                } else {
                    $average = 0;
                }

                return round(($average - 0.5) * 2, 2);

            }
        //});
    }
}
