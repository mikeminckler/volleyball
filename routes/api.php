<?php

Auth::routes();

Route::group(['middleware' => 'auth:api'], function() {

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


    Route::post('teams', ['as' => 'teams', 'uses' => 'TeamsController@teams']);
    Route::post('teams/{id}', ['as' => 'teams.store', 'uses' => 'TeamsController@store'])->where('id', '\d+');
    Route::post('teams/load/{id}', ['as' => 'teams.show', 'uses' => 'TeamsController@load'])->where('id', '\d+');
    Route::post('teams/create', ['as' => 'teams.create', 'uses' => 'TeamsController@create']);
    Route::post('teams/delete/{id}', ['as' => 'teams.delete', 'uses' => 'TeamsController@destroy'])->where('id', '\d+');
    Route::post('teams/players/{id}', ['as' => 'teams.players', 'uses' => 'TeamsController@players'])->where('id', '\d+');
    Route::post('teams/add-player/{id}', ['as' => 'teams.add-player', 'uses' => 'TeamsController@addPlayer'])->where('id', '\d+');
    Route::post('teams/delete-player/{id}', ['as' => 'teams.delete-player', 'uses' => 'TeamsController@removePlayer'])->where('id', '\d+');

});
