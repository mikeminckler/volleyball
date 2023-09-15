<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Game;

class Team extends Model
{
    use HasFactory;

    protected $visible = ['id', 'name', 'users', 'games'];

    public function saveTeam($input, $id = null) 
    {
        if ($id) {
            $team = Team::findOrFail($id);
        } else {
            $team = new Team();
        }

        $team->name = Arr::get($input, 'name');
        $team->save();

        return $team;
    }

    public function games() 
    {
        return $this->hasMany(Game::class, 'team1_id');
    }

    public function users() 
    {
        return $this->belongsToMany(User::class)->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function addUser(User $user) 
    {
        if (!$this->users()->get()->contains('id', $user->id)) {
            $this->users()->attach($user, ['sort_order' => $this->users()->count() + 1]);
        }
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
                $results->push(Team::where('name', 'like', '%'.$term.'%')->get());
            }

            return $results->flatten()->filter()->values();
        } else {
            return collect();
        }
    }

    public function getScore(Stat $stat, Game $game) 
    {
        $user_stats = UserStat::where('stat_id', $stat->id)
            ->where('game_id', $game->id)
            ->get();

        return $stat->calculateScore($user_stats);
    }
}
