<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\GameSet;

class Point extends Model
{
    public function gameSet()
    {
        return $this->belongsTo(GameSet::class);
    }
}
