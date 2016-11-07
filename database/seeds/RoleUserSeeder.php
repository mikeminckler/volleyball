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

        $user = User::where('email', 'mikeminckler@gmail.com')->first();
        $user->roles()->attach($admin);

        $role = new Role;
        $role->role_name = 'coach';
        $role->save();

        $role = new Role;
        $role->role_name = 'player';
        $role->save();

        $role = new Role;
        $role->role_name = 'team_manager';
        $role->save();

        $role = new Role;
        $role->role_name = 'club_manager';
        $role->save();

    }
}
