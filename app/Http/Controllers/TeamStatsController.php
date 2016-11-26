<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Team;
use App\Stat;
use App\Game;

class TeamStatsController extends Controller
{
    protected $team;
    protected $stat;
    protected $game;

    public function __construct(Team $team, Stat $stat, Game $game)
    {
        $this->team = $team;
        $this->stat = $stat;
        $this->game = $game;
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

    public function gameReport(Request $request, $id)
    {
        $games = $this->game->whereIn('id', $request->input('game_ids'))->get();
        return $this->team->findOrFail($id)->gameReport($games);
    }

    public function playersReport(Request $request, $id)
    {
        $games = $this->game->whereIn('id', $request->input('game_ids'))->get();
        return $this->team->findOrFail($id)->playersReport($games);
    }

}
