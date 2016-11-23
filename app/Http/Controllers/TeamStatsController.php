<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Team;
use App\Stat;

class TeamStatsController extends Controller
{
    protected $team;
    protected $stat;

    public function __construct(Team $team, Stat $stat)
    {
        $this->team = $team;
        $this->stat = $stat;
    }

    public function stats($id) 
    {
        return $this->team->findOrFail($id)->stats()->get()->sortBy(function($stat) {
            return $stat->id;
        })->values()->all();
    }

    public function getStat(Request $request, $id)
    {
        $team = $this->team->findOrFail($id);
        $stat = $this->stat->findOrFail($request->input('stat_id'));
        $type = $request->input('type');

        $team_stat = $team->stats()->where('stat_id', $stat->id)->first();

        if ($team_stat) {
            return $team_stat->pivot->{$type};
        } else {
            return null;
        }
    }

    public function setStat(Request $request, $id)
    {
        $team = $this->team->findOrFail($id);
        $stat = $this->stat->findOrFail($request->input('stat_id'));
        $type = $request->input('type');
        $value = $request->input('value');

        $team_stat = $team->stats()->where('stat_id', $stat->id)->first();

        if ($team_stat) {
            $team->stats()->updateExistingPivot($stat->id, [$type => $value]);
        } else {
            $team->stats()->attach($stat, [$type => $value]);
        }

        return $value;

    }

}
