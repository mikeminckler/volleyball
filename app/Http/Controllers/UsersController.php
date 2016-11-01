<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Events\PostMessage;

class UsersController extends Controller
{

    protected $user;

    public function __construct(User $user) 
    {
        $this->user = $user;
    }

    public function myInfo()
    {
        return auth()->user();
    }

}
