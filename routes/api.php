<?php

Auth::routes();

Route::group(['middleware' => 'auth:api'], function() {

    Route::post('home', ['as' => 'home', 'uses' => 'UsersController@home']);
    Route::post('menu', ['as' => 'menu', 'uses' => 'UsersController@menu']);

    Route::post('users', ['as' => 'users', 'uses' => 'UsersController@users']);
    Route::post('users/show/{id}', ['as' => 'users', 'uses' => 'UsersController@show']);
    Route::post('users/store/{id}', ['as' => 'users', 'uses' => 'UsersController@store']);

    Route::post('users/my-info', ['as' => 'users.my-info', 'uses' => 'UsersController@myInfo']);
    Route::post('save-my-account', ['as' => 'save-my-account', 'uses' => 'UsersController@saveMyAccount']);

});
