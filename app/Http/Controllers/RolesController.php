<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Role;

class RolesController extends Controller
{

    protected $role;

    public function __construct(Role $role) 
    {
        $this->role = $role;
    }

    public function index() {
        return $this->role->all();
    }
}
