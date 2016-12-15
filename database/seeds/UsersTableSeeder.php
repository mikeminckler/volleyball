<?php

use Illuminate\Database\Seeder;

use App\User;
use App\Role;
use App\Team;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $bcs = Team::where('team_name', 'Brentwood')->first();
        $admin = Role::where('role_name', 'admin')->first();

        $mike = new User;
        $mike->first_name = 'Mike';
        $mike->last_name = 'Minckler';
        $mike->email = 'mikeminckler@gmail.com';
        $mike->password = bcrypt('q');
        $mike->save();

        $mike->addRole($admin, $bcs);

    }
}
