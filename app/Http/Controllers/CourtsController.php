<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Cache;
use App\Court;
use App\Events\CourtUpdated;

class CourtsController extends Controller
{

    public function load()
    {

        $court_id = request()->input('court_id');
        $courts = Cache::get('courts');

        if (!$courts) {

            // create the cache and first court
            $courts = collect();
            $court = new Court($court_id);
            $courts->push($court);

        } else {

            $court = Court::getCourt($court_id);

            if (!$court) {
                $court = new Court($court_id);
                $courts->push($court);
            }
        
        }

        Cache::forever('courts', $courts);
        return response()->json(['court' => $court]);

    }


    public function addPoint()
    {

        $courts = Cache::get('courts');

        $court_id = request()->input('court_id');
        $team_id = request()->input('team_id');

        $courts->map(function($court) use($court_id, $team_id) {
            if ($court->id == $court_id) {
                $court->{'team'.$team_id}->points ++;
            }
            return $court;
        });

        Cache::forever('courts', $courts);
        event(new CourtUpdated($court_id));
        return response()->json(['success' => 'Added point']);

    }

    public function removePoint()
    {
    
        $courts = Cache::get('courts');

        $court_id = request()->input('court_id');
        $team_id = request()->input('team_id');

        $courts->map(function($court) use($court_id, $team_id) {
            if ($court->id == $court_id) {
                if ($court->{'team'.$team_id}->points > 0) {
                    $court->{'team'.$team_id}->points --;
                }
            }
            return $court;
        });

        Cache::forever('courts', $courts);
        event(new CourtUpdated($court_id));
        return response()->json(['success' => 'Removed point']);

    }

    public function addSet()
    {

        $courts = Cache::get('courts');

        $court_id = request()->input('court_id');
        $team_id = request()->input('team_id');

        $courts->map(function($court) use($court_id, $team_id) {
            if ($court->id == $court_id) {
                $court->{'team'.$team_id}->sets ++;
            }
            return $court;
        });

        Cache::forever('courts', $courts);
        event(new CourtUpdated($court_id));
        return response()->json(['success' => 'Added set']);


    }

    public function removeSet()
    {
    
        $courts = Cache::get('courts');

        $court_id = request()->input('court_id');
        $team_id = request()->input('team_id');

        $courts->map(function($court) use($court_id, $team_id) {
            if ($court->id == $court_id) {
                if ($court->{'team'.$team_id}->sets > 0) {
                    $court->{'team'.$team_id}->sets --;
                }
            }
            return $court;
        });

        Cache::forever('courts', $courts);
        event(new CourtUpdated($court_id));
        return response()->json(['success' => 'Removed set']);


    }

    public function setTeamName()
    {
    
        $courts = Cache::get('courts');

        $court_id = request()->input('court_id');
        $team_id = request()->input('team_id');

        $courts->map(function($court) use($court_id, $team_id) {
            if ($court->id == $court_id) {
                $court->{'team'.$team_id}->name = request()->input('team_name');
            }
            return $court;
        });

        Cache::forever('courts', $courts);
        event(new CourtUpdated($court_id));
        return response()->json(['success' => 'Updated name']);

    }

    public function resetScores()
    {

        $courts = Cache::get('courts');

        $court_id = request()->input('court_id');

        $courts->map(function($court) use($court_id) {
            if ($court->id == $court_id) {
                $court->team1->points = 0;
                $court->team2->points = 0;
            }
            return $court;
        });

        Cache::forever('courts', $courts);
        event(new CourtUpdated($court_id));
        return response()->json(['success' => 'Updated name']);

    }
}
