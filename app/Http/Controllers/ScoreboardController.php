<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Court;

class ScoreboardController extends Controller
{

    public function index()
    {
        return view('scoreboard.index');
    }

    public function court($court_id)
    {
        $court = Court::getCourt($court_id);
        if (!$court) {
            return redirect()->route('scoreboard')->with(['error' => 'There is no court with the number']);
        }
        return view('scoreboard.court', compact('court'));
    }
}
