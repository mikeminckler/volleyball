<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Role;
use App\Models\Team;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];
     */

    protected $visible = ['id', 'name', 'email', 'nickname', 'currentTeam'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function currentTeam() 
    {
        return $this->belongsTo(Team::class);
    }

    private function resolveRole($role) {

        if (is_int($role)) {
            $role = Role::findOrFail($role);
        }

        if (!$role instanceof Role) {
            $role_name = $role;
            $role = Role::where('name', $role)->first();
        }

        if (!$role instanceof Role) {
            throw new ModelNotFoundException('There is no role with the name '.$role_name);
        }

        return $role;
    }

    public function addRole($role)
    {
        $role = $this->resolveRole($role);

        if (!$this->roles->contains('id', $role->id)) {
            $this->roles()->attach($role);
        }

        return $this;
    }

    public function removeRole($role)
    {
        $role = $this->resolveRole($role);
        $this->roles()->detach($role);
        return $this;
    }

    public function hasRole($role)
    {
        $role = $this->resolveRole($role);

        if ($this->roles->contains('name', 'admin')) {
            return true;
        }

        return $this->roles->contains('id', $role->id);
    }
}
