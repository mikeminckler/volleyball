<?php

Auth::routes();

Route::group(['middleware' => 'auth:api', ], function() {

    Route::post('home', ['as' => 'home', 'uses' => 'UsersController@home']);
    Route::post('menu', ['as' => 'menu', 'uses' => 'UsersMenuController@index']);

    Route::post('users', ['as' => 'users', 'uses' => 'UsersController@users']);
    Route::post('users/my-info', ['as' => 'users.my-info', 'uses' => 'UsersController@myInfo']);
    Route::post('users/my-roles', ['as' => 'users.my-info', 'uses' => 'UsersController@myRoles']);
    Route::post('users/load/{id}', ['as' => 'users.show', 'uses' => 'UsersController@load'])->where('id', '\d+');
    Route::post('users/roles/{id}', ['as' => 'users.roles', 'uses' => 'UsersController@roles'])->where('id', '\d+');
    Route::post('users/{id}', ['as' => 'users.store', 'uses' => 'UsersController@store'])->where('id', '\d+');
    Route::post('my-account', ['as' => 'users.save-my-info', 'uses' => 'UsersController@saveMyInfo']);
    Route::post('users/create', ['as' => 'users.create', 'uses' => 'UsersController@create']);
    Route::post('users/delete/{id}', ['as' => 'users.delete', 'uses' => 'UsersController@destroy'])->where('id', '\d+');

    Route::post('roles', ['as' => 'roles', 'uses' => 'RolesController@index']);
    
    Route::post('users/roles/{id}', ['as' => 'users.roles', 'uses' => 'UsersController@roles'])->where('id', '\d+');
    Route::post('users/save-role/{id}', ['as' => 'users.save-role', 'uses' => 'UsersController@saveRole'])->where('id', '\d+');
    Route::post('users/remove-role/{id}', ['as' => 'users.remove-role', 'uses' => 'UsersController@removeRole'])->where('id', '\d+');

    // Search routes
    Route::post('search/users', ['as' => 'search.users', 'uses' => 'SearchController@users']);
    Route::post('search/players', ['as' => 'search.players', 'uses' => 'SearchController@players']);
    Route::post('search/teams', ['as' => 'search.teams', 'uses' => 'SearchController@teams']);


    Route::post('teams', ['as' => 'teams', 'uses' => 'TeamsController@teams']);
    Route::post('teams/{id}', ['as' => 'teams.store', 'uses' => 'TeamsController@store'])->where('id', '\d+');
    Route::post('teams/load/{id}', ['as' => 'teams.show', 'uses' => 'TeamsController@load'])->where('id', '\d+');
    Route::post('teams/create', ['as' => 'teams.create', 'uses' => 'TeamsController@create']);
    Route::post('teams/delete/{id}', ['as' => 'teams.delete', 'uses' => 'TeamsController@destroy'])->where('id', '\d+');
    Route::post('teams/players/{id}', ['as' => 'teams.players', 'uses' => 'TeamsController@players'])->where('id', '\d+');
    Route::post('teams/add-player/{id}', ['as' => 'teams.add-player', 'uses' => 'TeamsController@addPlayer'])->where('id', '\d+');
    Route::post('teams/delete-player/{id}', ['as' => 'teams.delete-player', 'uses' => 'TeamsController@removePlayer'])->where('id', '\d+');
    Route::post('teams/get-stat/{id}', ['as' => 'teams.get-stat', 'uses' => 'TeamStatsController@getStat'])->where('id', '\d+');
    Route::post('teams/set-stat/{id}', ['as' => 'teams.set-stat', 'uses' => 'TeamStatsController@setStat'])->where('id', '\d+');
    Route::post('teams/stats/{id}', ['as' => 'teams.stats', 'uses' => 'TeamStatsController@stats'])->where('id', '\d+');
    Route::post('teams/games/{id}', ['as' => 'teams.games', 'uses' => 'TeamsController@games'])->where('id', '\d+');
    Route::post('teams/game-report/{id}', ['as' => 'teams.game-report', 'uses' => 'TeamStatsController@gameReport'])->where('id', '\d+');
    Route::post('teams/players-stats-report/{id}', ['as' => 'teams.players-stats-report', 'uses' => 'TeamStatsController@playersReport'])->where('id', '\d+');

    Route::post('games', ['as' => 'games', 'uses' => 'GamesController@games']);
    Route::post('games/load/{id}', ['as' => 'games.show', 'uses' => 'GamesController@load'])->where('id', '\d+');
    Route::post('games/{id}', ['as' => 'games.store', 'uses' => 'GamesController@store'])->where('id', '\d+');
    Route::post('games/create', ['as' => 'games.create', 'uses' => 'GamesController@create']);
    Route::post('games/delete/{id}', ['as' => 'games.delete', 'uses' => 'GamesController@destroy'])->where('id', '\d+');
    Route::post('games/sets/{id}', ['as' => 'games.sets', 'uses' => 'GamesController@gameSets'])->where('id', '\d+');
    Route::post('games/add-point/{id}', ['as' => 'games.add-point', 'uses' => 'GamesController@addPoint'])->where('id', '\d+');
    Route::post('games/remove-point/{id}', ['as' => 'games.remove-point', 'uses' => 'GamesController@removePoint'])->where('id', '\d+');
    Route::post('games/add-set/{id}', ['as' => 'games.add-set', 'uses' => 'GamesController@addSet'])->where('id', '\d+');

    Route::post('stats', ['as' => 'stats', 'uses' => 'StatsController@stats']);
    Route::post('stats/{id}', ['as' => 'stats.store', 'uses' => 'StatsController@store'])->where('id', '\d+');
    Route::post('stats/load/{id}', ['as' => 'stats.show', 'uses' => 'StatsController@load'])->where('id', '\d+');
    Route::post('stats/create', ['as' => 'stats.create', 'uses' => 'StatsController@create']);
    Route::post('stats/delete/{id}', ['as' => 'stats.delete', 'uses' => 'StatsController@destroy'])->where('id', '\d+');


    Route::post('players/load/{id}', ['as' => 'players.load', 'uses' => 'PlayersController@load'])->where('id', '\d+');
    Route::post('players/games/{id}', ['as' => 'players.games', 'uses' => 'PlayersController@games'])->where('id', '\d+');
    Route::post('players/game-report/{id}', ['as' => 'players.game-report', 'uses' => 'PlayersController@gameReport'])->where('id', '\d+');

    Route::post('players/get-game-stat-score/{id}', ['as' => 'players.get-game-stat-score', 'uses' => 'PlayerGameController@getStatScore'])->where('id', '\d+');
    Route::post('players/add-game-stat-score/{id}', ['as' => 'players.add-game-stat-score', 'uses' => 'PlayerGameController@addStatScore'])->where('id', '\d+');

    Route::post('charts/team-games', ['as' => 'charts.team-games', 'uses' => 'ChartsController@teamGames']);
    Route::post('charts/player-games', ['as' => 'charts.player-games', 'uses' => 'ChartsController@playerGames']);

});
