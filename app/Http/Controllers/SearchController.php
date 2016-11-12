<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Search;

class SearchController extends Controller
{

    protected $search;

    public function __construct(Search $search)
    {
        $this->search = $search;
    }

    public function users(Request $request)
    {
        return $this->search->search('user', $request->only('terms'));
    }

    public function players(Request $request)
    {
        return $this->search->search('player', $request->only('terms'));
    }

}
