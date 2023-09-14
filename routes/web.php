<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/', [HomeController::class, 'home'])->name('home');

    Route::get('teams', [TeamsController::class, 'index'])->name('teams.index');
    Route::post('teams/select', [TeamsController::class, 'select'])->name('teams.select');
    Route::post('teams/reset', [TeamsController::class, 'resetTeam'])->name('teams.reset');
    Route::post('teams/create', [TeamsController::class, 'store'])->name('teams.create');
    Route::post('teams/search', [TeamsController::class, 'search'])->name('teams.search');
    Route::post('teams/{id}', [TeamsController::class, 'store'])->name('teams.store')->where('id', '\d+');
    Route::post('teams/{id}/add-player', [TeamsController::class, 'addPlayer'])->name('teams.add-player');

    Route::post('games/create', [GamesController::class, 'store'])->name('games.create');
    Route::get('games/{id}', [GamesController::class, 'view'])->name('games.view')->where('id', '\d+');

    Route::post('users/create', [UsersController::class, 'store'])->name('users.create');
    Route::post('users/search', [UsersController::class, 'search'])->name('users.search');
});
