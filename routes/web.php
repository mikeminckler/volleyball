<?php

Route::get('{catchall}', ['as' => 'home', 'uses' => 'HomeController@index'])->where('catchall', '(.*)');

