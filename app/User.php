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

use App\Events\UserUpdated;

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

    public function saveUser($input, $id = null) {

        $messages = [];

        if ($id) {
            $user = $this->findOrFail($id);
        } else {
            $user = new User;
        }

        $user->first_name = $input['first_name'];
        $user->last_name = $input['last_name'];
        $user->email = $input['email'];

        if (array_key_exists('password', $input)) {
            if (strlen(trim($input['password'])) > 0) {
                $user->password = bcrypt($input['password']);
                $messages[] = 'Your password has been updated';
            }
        }

        $user->save();

        $messages[] = 'Your info has been updated';

        // broadcast an update event so our info is up to date in browser

        foreach ($messages as $message) {

            if (!auth()->user()->id == $user->id) {
                $message .= ' by '.auth()->user()->full_name;
            }

            event(new UserUpdated($message, $user));
        }

        return $user;
    }

}
