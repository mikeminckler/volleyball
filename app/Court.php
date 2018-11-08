<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Cache;

class Court extends Model {

    public function __construct($id) {
    
        $this->id = $id;
        $this->team1 = new CourtTeam(1);
        $this->team2 = new CourtTeam(2);

    }

    public static function getCourt($court_id)
    {
        return Cache::get('courts')->first(function ($court) use($court_id) {
            return $court->id == $court_id;
        });
    }

}

class CourtTeam {

    public function __construct($id) {
        $this->id = $id;
        $this->points = 0;
        $this->sets = 0;
        $this->name = '';
    }

}
