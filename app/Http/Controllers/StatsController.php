<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Stat;

class StatsController extends Controller
{
    
    protected $stat;

    public function __construct(Stat $stat) 
    {
        $this->stat = $stat;
    }


    public function stats()
    {
        return $this->stat->where('removed', false)->get()
            ->sortBy('stat_name')
            ->values()
            ->all();
    }

    public function create(Requests\StatSave $request)
    {
        $stat = new Stat;
        return $stat
            ->saveStat($request->only('stat_name'));
    }

    public function load($id)
    {
        return $this->stat->findOrFail($id);
    }

    public function store(Requests\StatSave $request, $id)
    {
        return $this->stat->findOrFail($id)
            ->saveStat($request->only('stat_name'));
    }

    public function destroy(Request $request, $id)
    {
        return $this->stat->findOrFail($id)->remove();
    }

}
