<?php

use Illuminate\Database\Seeder;

use App\User;
use App\Role;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $admin = new Role;
        $admin->role_name = 'admin';
        $admin->save();

        $role = new Role;
        $role->role_name = 'coach';
        $role->save();

        $role = new Role;
        $role->role_name = 'player';
        $role->save();

        $role = new Role;
        $role->role_name = 'manager';
        $role->save();

        $role = new Role;
        $role->role_name = 'stats';
        $role->save();

    }
}
