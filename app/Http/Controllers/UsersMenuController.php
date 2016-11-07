<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersMenuController extends Controller
{
    public function index()
    {
        $menu = [];
        $menu[] = ['url' => 'users', 'name' => 'Users', 'roles' => ['admin']];
        return $menu;
    }
}
