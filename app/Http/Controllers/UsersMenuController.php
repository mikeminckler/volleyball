<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersMenuController extends Controller
{
    public function index()
    {
        $menu = [];

        // admin
        $menu[] = ['url' => 'users', 'name' => 'Users', 'roles' => ['admin']];
        $menu[] = ['url' => 'games', 'name' => 'All Games', 'roles' => ['admin']];
        $menu[] = ['url' => 'teams', 'name' => 'Teams', 'roles' => ['admin']];
        $menu[] = ['url' => 'stats', 'name' => 'Stats', 'roles' => ['admin']];

        $menu[] = ['url' => 'teams/manage-team', 'name' => 'Manage Team', 'roles' => ['admin', 'manager']];
        $menu[] = ['url' => 'teams/games/all', 'name' => 'Games', 'roles' => ['admin', 'manager', 'coach']];

        return $menu;
    }
}
