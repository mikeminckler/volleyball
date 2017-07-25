<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\User;
use App\Team;
use App\Role;
use App\Events\UserRemoved;

class UsersController extends Controller
{

    protected $user;
    protected $team;
    protected $role;

    public function __construct(User $user, Team $team, Role $role) 
    {
        $this->user = $user;
        $this->team = $team;
        $this->role = $role;
    }

    public function myInfo()
    {
        return auth()->user()->load('roles');
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
            ->saveUser($request->only('first_name', 'common_name', 'last_name', 'email', 'password'));
    }

    public function load($id)
    {
        return $this->user->findOrFail($id)->load('roles');
    }

    public function store(Requests\UserSave $request, $id)
    {
        return $this->user->findOrFail($id)
            ->saveUser($request->only('first_name', 'common_name', 'last_name', 'email', 'password'));
    }

    public function saveMyInfo(Requests\UserSave $request)
    {
        return auth()->user()
            ->saveUser($request->only('first_name', 'common_name', 'last_name', 'email', 'password'));
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
        $team = $this->team->findOrFail($request->input('team_id'));
        return $this->user->findOrFail($id)
            ->addRole($request->input('role_id'), $team);
    }

    public function removeRole(Request $request, $id) 
    {
        $team = $this->team->findOrFail($request->input('team_id'));
        $role = $this->role->findOrFail($request->input('role_id'));
        return $this->user->findOrFail($id)
            ->removeRole($role, $team);
    }

    public function loginCheck(Request $request)
    {

        if (auth()->check()) {
            return response()->json(['status' => 'ok']);
        } else {
            return response()->json(['status' => 'timeout']);
        }

    }

    public function setTeam($id)
    {
        $team = $this->team->findOrFail($id);
        session()->put('team_id', $team->id);
        return $team;
    }

}
