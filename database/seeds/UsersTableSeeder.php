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

        // Define some roles so we can attach users to them as we go
        $admin = Role::where('role_name', 'admin')->first();

        $mike = new User;
        $mike->first_name = 'Mike';
        $mike->last_name = 'Minckler';
        $mike->email = 'mikeminckler@gmail.com';
        $mike->password = bcrypt('q');
        $mike->save();

        $bcs = Team::where('team_name', 'Brentwood')->first();

        $mike->addRole($admin, $bcs);

        /*
        $jb = new User;
        $jb->first_name = 'John';
        $jb->last_name = 'Bonham';
        $jb->email = 'bonzo@zep.com';
        $jb->password = bcrypt('q');
        $jb->save();
        $zep->addPlayer($jb);

        $jp = new User;
        $jp->first_name = 'Jimmy';
        $jp->last_name = 'Page';
        $jp->email = 'jimmy@zep.com';
        $jp->password = bcrypt('q');
        $jp->save();
        $zep->addPlayer($jp);

        $jpj = new User;
        $jpj->first_name = 'John Paul';
        $jpj->last_name = 'Jones';
        $jpj->email = 'jpj@zep.com';
        $jpj->password = bcrypt('q');
        $jpj->save();
        $zep->addPlayer($jpj);

        $rp = new User;
        $rp->first_name = 'Robert';
        $rp->last_name = 'Plant';
        $rp->email = 'percy@zep.com';
        $rp->password = bcrypt('q');
        $rp->save();
        $zep->addPlayer($rp);

        $stones = Team::where('team_name', 'Rolling Stones')->first();

        $mj = new User;
        $mj->first_name = 'Mic';
        $mj->last_name = 'Jagger';
        $mj->email = 'mic@stones.com';
        $mj->password = bcrypt('q');
        $mj->save();
        $stones->addPlayer($mj);

        $kr = new User;
        $kr->first_name = 'Keith';
        $kr->last_name = 'Richards';
        $kr->email = 'keith@stones.com';
        $kr->password = bcrypt('q');
        $kr->save();
        $stones->addPlayer($kr);

        $rw = new User;
        $rw->first_name = 'Ronnie';
        $rw->last_name = 'Wood';
        $rw->email = 'ronnie@stones.com';
        $rw->password = bcrypt('q');
        $rw->save();
        $stones->addPlayer($rw);

        $bj = new User;
        $bj->first_name = 'Brian';
        $bj->last_name = 'Jones';
        $bj->email = 'brian@stones.com';
        $bj->password = bcrypt('q');
        $bj->save();
        $stones->addPlayer($bj);

        $cw = new User;
        $cw->first_name = 'Charlie';
        $cw->last_name = 'Watts';
        $cw->email = 'charlie@stones.com';
        $cw->password = bcrypt('q');
        $cw->save();
        $stones->addPlayer($cw);

        $floyd = Team::where('team_name', 'Pink Floyd')->first();

        $dg = new User;
        $dg->first_name = 'David';
        $dg->last_name = 'Gilmore';
        $dg->email = 'david@floyd.com';
        $dg->password = bcrypt('q');
        $dg->save();
        $floyd->addPlayer($dg);

        $rw = new User;
        $rw->first_name = 'Roger';
        $rw->last_name = 'Waters';
        $rw->email = 'roger@floyd.com';
        $rw->password = bcrypt('q');
        $rw->save();
        $floyd->addPlayer($rw);

        $nm = new User;
        $nm->first_name = 'Nick';
        $nm->last_name = 'Mason';
        $nm->email = 'nick@floyd.com';
        $nm->password = bcrypt('q');
        $nm->save();
        $floyd->addPlayer($nm);

        $sb = new User;
        $sb->first_name = 'Sid';
        $sb->last_name = 'Barrett';
        $sb->email = 'sid@floyd.com';
        $sb->password = bcrypt('q');
        $sb->save();
        $floyd->addPlayer($sb);

        $rw = new User;
        $rw->first_name = 'Richard';
        $rw->last_name = 'Wright';
        $rw->email = 'richard@floyd.com';
        $rw->password = bcrypt('q');
        $rw->save();
        $floyd->addPlayer($rw);
        */
    }
}
