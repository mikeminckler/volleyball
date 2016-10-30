<?php

Auth::routes();

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('users/my-info', ['as' => 'users.my-info', 'uses' => 'UsersController@myInfo']);
});
