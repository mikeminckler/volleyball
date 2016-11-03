<?php

Auth::routes();

Route::group(['middleware' => 'auth:api'], function() {

    Route::post('home', ['as' => 'home', 'uses' => 'UsersController@home']);
    Route::post('menu', ['as' => 'menu', 'uses' => 'UsersController@menu']);

    Route::post('users', ['as' => 'users', 'uses' => 'UsersController@users']);
    Route::post('users/my-info', ['as' => 'users.my-info', 'uses' => 'UsersController@myInfo']);
    Route::post('users/load/{id}', ['as' => 'users.show', 'uses' => 'UsersController@load'])->where('id', '\d+');
    Route::post('users/{id}', ['as' => 'users.store', 'uses' => 'UsersController@store'])->where('id', '\d+');
    Route::post('my-account', ['as' => 'users.save-my-info', 'uses' => 'UsersController@saveMyInfo']);
    Route::post('users/create', ['as' => 'users.create', 'uses' => 'UsersController@create']);


});
