<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Role;
use App\Models\Team;
use App\Models\Stat;
use App\Models\Game;
use App\Models\UserStat;

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

    public function saveUser($input, $id = null) 
    {
        if ($id) {
            $user = User::findOrFail($id);
        } else {
            $user = new User();
        }

        $user->name = Arr::get($input, 'name');
        $email = Arr::get($input, 'email');
        if (!$email) {
            $email = collect(explode(' ', Str::lower(Arr::get($input, 'name'))))->filter()->implode('.').'@brentwood.ca';
        }
        if (!$user->password) {
            $user->password = bcrypt(Str::random(12));
        }
        $user->email = $email;
        $user->nickname = Arr::get($input, 'nickname');
        $user->save();

        return $user;
    }

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

    public function search() 
    {
        $terms = request('terms');

        if (Str::length($terms) > 2) {
            $terms = collect(explode(' ', $terms));

            $terms = $terms->filter(function ($term) {
                return Str::length($term) > 2;
            });

            $results = collect();

            foreach ($terms as $term) {
                $results->push(
                    User::where('name', 'like', '%'.$term.'%')
                        ->orWhere('nickname', 'like', '%'.$term.'%')
                        ->get()
                );
            }

            return $results->flatten()->filter()->values();
        } else {
            return collect();
        }
    }

    public function getScore(Stat $stat, Game $game) 
    {
        $user_stats = UserStat::where('user_id', $this->id)
            ->where('stat_id', $stat->id)
            ->where('game_id', $game->id)
            ->get();

        return $stat->calculateScore($user_stats);
    }
}
