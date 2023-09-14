<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UsersController extends Controller
{
    public function search() 
    {
        return (new User)->search();
    }

    public function store($id = null) 
    {
        request()->validate([
            'name' => 'required',
        ]);

        $user = (new User)->saveUser(request()->only(['name', 'nickname', 'email']), $id);

        if (request('json')) {
            return response()->json([
                'user' => $user,
            ]);
        } else {
            return redirect()->route('users.index');
        }
    }
}
