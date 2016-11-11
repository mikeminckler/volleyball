<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersMenuController extends Controller
{
    public function index()
    {
        $menu = [];
        $menu[] = ['url' => 'users', 'name' => 'Users', 'roles' => ['admin']];
        $menu[] = ['url' => 'teams', 'name' => 'Teams', 'roles' => ['admin']];
        return $menu;
    }
}
