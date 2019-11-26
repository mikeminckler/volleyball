<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Role;
use App\Player;

use Illuminate\Support\Collection;

use App\Events\UserUpdated;
use App\Events\UserCreated;
use App\Events\UserRemoved;
use App\Events\UserRolesUpdated;
use App\Events\UsersRefresh;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'common_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function player()
    {
        return $this->hasOne('App\Player');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function saveUser($input)
    {
        $messages = [];

        if (!$this->id) {
            $created = true;
        } else {
            $created = false;
        }

        $this->first_name = $input['first_name'];
        $this->common_name = $input['common_name'];
        $this->last_name = $input['last_name'];
        $this->email = $input['email'];

        if (array_key_exists('password', $input)) {
            if (strlen(trim($input['password'])) > 0) {
                $this->password = bcrypt($input['password']);
                $messages[] = 'Your password has been updated';
            }
        }

        $this->save();

        $messages[] = 'Your info has been updated';

        // broadcast an update event so our info is up to date in browser

        foreach ($messages as $message) {
            if (auth()->user()->id != $this->id) {
                $message .= ' by '.auth()->user()->full_name;
            }

            event(new UserUpdated($message, $this));
        }

        if ($created) {
            event(new UserCreated($this->full_name.' has been created'));
        }

        event(new UsersRefresh());

        return $this;
    }

    public function roles()
    {
        return $this->belongsToMany('App\Role')->withPivot('team_id');
    }

    public function addRole($role, $team)
    {
        if (!$role instanceof Role) {
            if (is_numeric($role)) {
                $role = Role::findOrFail($role);
            } else {
                $role = Role::where('role_name', $role)->first();
            }
        }

        if (!$role instanceof Role) {
            return false;
        }

        if (!$this->roles->contains(function ($user_role) use ($role, $team) {
            if ($user_role->pivot->team_id == $team->id && $user_role->id == $role->id) {
                return true;
            }
            return false;
        })) {
            $this->roles()->attach($role, ['team_id' => $team->id]);
            event(new UserRolesUpdated('You are now in the '.$role->role_name.' group for '.$team->team_name, $this));
        }

        return $this;
    }

    public function removeRole($role, $team)
    {
        if (!$role instanceof Role) {
            $role = Role::findOrFail($role);
        }

        if ($this->roles->contains(function ($user_role) use ($role, $team) {
            if ($user_role->pivot->team_id == $team->id && $user_role->id == $role->id) {
                return true;
            }
            return false;
        })) {
            $this->roles()->newPivotStatementForId($role->id)->where('team_id', $team->id)->delete();
            event(new UserRolesUpdated('You have been removed from the '.$role->role_name.' group for '.$team->id, $this));
        }

        return $this;
    }

    public function search($term)
    {
        return $this->where('removed', false)
            ->where(function ($query) use ($term) {
                $query->where('first_name', 'like', '%'.$term.'%')
                    ->orWhere('last_name', 'like', '%'.$term.'%')
                    ->orWhere('common_name', 'like', '%'.$term.'%');
            })->get();
    }

    public function searchResultsArray($objects)
    {
        $users = array();
        foreach ($objects as $user) {
            $user_array = array();
            $user_array['id'] = $user->id;
            $user_array['value'] = $user->full_name;
            $user_array['label'] = $user->full_name;
            $user_array['selected'] = false;
            $users[] = $user_array;
        }

        return $users;
    }
}
