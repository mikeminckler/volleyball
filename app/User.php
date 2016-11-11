<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Role;

use App\Events\UserUpdated;
use App\Events\UserCreated;
use App\Events\UserRemoved;
use App\Events\UserRolesUpdated;
use App\Events\UsersRefresh;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTSubject
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */

    public function getJWTCustomClaims()
    {
        return [
            'userid' => $this->id,
            'email' => $this->email,
            'name' => $this->full_name
        ];
    }

    public function getFullNameAttribute() 
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function saveUser($input) {

        $messages = [];

        if (!$this->id) {
            $created = true;
        } else {
            $created = false;
        }

        $this->first_name = $input['first_name'];
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
        return $this->belongsToMany('App\Role');
    }

    public function addRole($role)
    {

        if (!$role instanceof Role) {
            $role = Role::findOrFail($role);
        }

        if (!$this->roles->contains($role)) {
            $this->roles()->attach($role);
            event(new UserRolesUpdated('You are now in the '.$role->role_name.' group', $this));
        }

        return $this;
    }

    public function removeRole($role)
    {

        if (!$role instanceof Role) {
            $role = Role::findOrFail($role);
        }

        if ($this->roles->contains($role)) {
            $this->roles()->detach($role);
            event(new UserRolesUpdated('You have been removed from the '.$role->role_name.' group', $this));
        }

        return $this;
    }

    public function search($term)
    {
        return $this->where('hidden', '0')
            ->where(function($query) use($term) {
                $query->where('first_name', 'like', '%'.$term.'%')
                    ->orWhere('last_name', 'like', '%'.$term.'%');
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
                $users[] = $user_array;
        }

        return $users;
    }

}
