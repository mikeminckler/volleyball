<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\User;
use App\Events\UserRemoved;

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

    public function myRoles()
    {
        return auth()->user()->roles->pluck('role_name');
    }

    public function users()
    {
        return $this->user->where('removed', false)->get()
            ->sortBy('last_name')
            ->values()
            ->all();
    }

    public function create(Requests\UserCreate $request)
    {
        $user = new User;
        return $this->user
            ->saveUser($request->only('first_name', 'last_name', 'email', 'password'));
    }

    public function load($id)
    {
        return $this->user->findOrFail($id);
    }

    public function store(Requests\UserSave $request, $id)
    {
        return $this->user->findOrFail($id)
            ->saveUser($request->only('first_name', 'last_name', 'email', 'password'));
    }

    public function saveMyInfo(Requests\UserSave $request)
    {
        return auth()->user()
            ->saveUser($request->only('first_name', 'last_name', 'email', 'password'));
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->user->findOrFail($id);
        $user->removed = true;
        $user->save();
        event(new UserRemoved($user->full_name.' has been removed'));
        return $user;
    }

    public function roles($id) 
    {
        return $this->user->findOrFail($id)->roles->pluck('id');
    }

    public function saveRole(Request $request, $id) 
    {
        return $this->user->findOrFail($id)->addRole($request->input('role_id'));
    }

    public function removeRole(Request $request, $id) 
    {
        return $this->user->findOrFail($id)->removeRole($request->input('role_id'));
    }

}
