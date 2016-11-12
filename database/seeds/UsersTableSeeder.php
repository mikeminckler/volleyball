<?php

use Illuminate\Database\Seeder;

use App\User;
use App\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Define some roles so we can attach users to them as we go
        $admin = Role::where('role_name', 'admin')->first();

        $mike = new User;
        $mike->first_name = 'Mike';
        $mike->last_name = 'Minckler';
        $mike->email = 'mikeminckler@gmail.com';
        $mike->password = bcrypt('q');
        $mike->save();

        $mike->addRole($admin);

        $jb = new User;
        $jb->first_name = 'John';
        $jb->last_name = 'Bonham';
        $jb->email = 'bonzo@zep.com';
        $jb->password = bcrypt('q');
        $jb->save();

        $jp = new User;
        $jp->first_name = 'Jimmy';
        $jp->last_name = 'Page';
        $jp->email = 'jimmy@zep.com';
        $jp->password = bcrypt('q');
        $jp->save();

        $jpj = new User;
        $jpj->first_name = 'John Paul';
        $jpj->last_name = 'Jones';
        $jpj->email = 'jpj@zep.com';
        $jpj->password = bcrypt('q');
        $jpj->save();

        $rp = new User;
        $rp->first_name = 'Robert';
        $rp->last_name = 'Plant';
        $rp->email = 'percy@zep.com';
        $rp->password = bcrypt('q');
        $rp->save();

    }
}
