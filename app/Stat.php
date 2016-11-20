<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Events\StatRemoved;
use App\Events\StatCreated;
use App\Events\StatUpdated;
use App\Events\StatsRefresh;

class Stat extends Model
{

    public function saveStat($input) {
        
        if (!$this->id) {
            $created = true;
        } else {
            $created = false;
        }

        $this->stat_name = $input['stat_name'];
        $this->save();

        if ($created) {
            event(new StatCreated($this->stat_name.' has been created'));
        }

        event(new StatsRefresh());

        return $this;
    }

    public function remove()
    {
        $this->removed = true;
        $this->save();
        event(new StatRemoved($this->stat_name.' has been removed'));
        event(new StatsRefresh());
        return $this;
    }
}
