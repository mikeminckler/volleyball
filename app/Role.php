<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $appends = ['team', 'name'];

    public function users()
    {
        return $this->belongsToMany('App\User')->where('removed', false);
    }

    public function getTeamAttribute()
    {
        return Team::find($this->pivot->team_id);
    }


    public function search($term)
    {
        return $this->where('role_name', 'like', '%'.$term.'%')->get();
    }

    public function getNameAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->role_name));
    }

    public function searchResultsArray($objects)
    {
        $roles = array();
        foreach ($objects as $role) {
                $role_array = array();
                $role_array['id'] = $role->id;
                $role_array['value'] = $role->role_name;
                $role_array['label'] = $role->name;
                $role_array['selected'] = false;
                $roles[] = $role_array;
        }

        return $roles;
    }
}
