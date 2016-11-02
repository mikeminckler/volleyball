<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UsersController extends Controller
{

    protected $user;

    public function __construct(User $user) 
    {
        $this->user = $user;
    }

    public function myInfo()
    {
        return auth()->user();
    }

    public function users()
    {
        return $this->user->all();
    }

    public function create(Request $request)
    {
        $user = $this->user->saveUser($request->all());
        return $user;
    }

    public function show($id)
    {
        $user = $this->user->findOrFail($id);
        return $user;
    }

    public function store(Request $request, $id)
    {
        $user = $this->user->saveUser($request->all(), $id);
        return $user;
    }

    public function saveMyAccount(Request $request)
    {
        $user = $this->user->saveUser($request->all(), auth()->user()->id);
        return $user;
    }

    public function menu()
    {

        $menu = [];
        $menu[] = ['url' => 'users', 'name' => 'Users'];

        return $menu;
    }

}
