<?php

Route::get('scoreboard', 'ScoreboardController@index')->name('scoreboard');
Route::get('{catchall}', ['as' => 'home', 'uses' => 'HomeController@index'])->where('catchall', '(.*)');

