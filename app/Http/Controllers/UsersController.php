<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

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

    public function create(Requests\UserCreate $request)
    {
        $user = $this->user->saveUser($request->all());
        return $user;
    }

    public function load($id)
    {
        $user = $this->user->findOrFail($id);
        return $user;
    }

    public function store(Requests\UserSave $request, $id)
    {
        $user = $this->user->saveUser($request->all(), $id);
        return $user;
    }

    public function saveMyInfo(Requests\UserSave $request)
    {
        $user = $this->user->saveUser($request->all(), $request->input('id'));
        return $user;
    }

    public function menu()
    {

        $menu = [];
        $menu[] = ['url' => 'users', 'name' => 'Users'];

        return $menu;
    }

}
