<?php

Route::get('scoreboard', 'ScoreboardController@index')->name('scoreboard');
Route::get('scoreboard/{court_id}', 'ScoreboardController@court')->name('scoreboard.court');
Route::get('{catchall}', ['as' => 'home', 'uses' => 'HomeController@index'])->where('catchall', '(.*)');

