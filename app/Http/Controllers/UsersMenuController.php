<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersMenuController extends Controller
{
    public function index()
    {
        $menu = [];
        $menu[] = ['url' => 'users', 'name' => 'Users', 'roles' => ['admin']];
        $menu[] = ['url' => 'games', 'name' => 'Games', 'roles' => ['admin']];
        $menu[] = ['url' => 'teams', 'name' => 'Teams', 'roles' => ['admin']];
        $menu[] = ['url' => 'stats', 'name' => 'Stats', 'roles' => ['admin']];
        return $menu;
    }
}
