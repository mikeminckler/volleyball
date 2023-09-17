<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

use App\Models\Team;

class HomeController extends Controller
{
    public function home() 
    {
        $currentTeam = auth()->user()->currentTeam?->load(['games.team1', 'games.team2', 'users']);

        return inertia('Home', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'teams' => Team::all(),
            'currentTeam' => $currentTeam,
            'games' => $currentTeam?->games()->with(['team1', 'team2'])->paginate(),
        ]);
    }
}
