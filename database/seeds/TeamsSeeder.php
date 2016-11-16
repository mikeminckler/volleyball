<?php

use Illuminate\Database\Seeder;

use App\Team;

class TeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bcs = new Team;
        $bcs->team_name = 'Brentwood';
        $bcs->initials = 'BCS';
        $bcs->save();
            
        $team = new Team;
        $team->team_name = 'Led Zeppelin';
        $team->initials = 'LZ';
        $team->save();

        $team = new Team;
        $team->team_name = 'Pink Floyd';
        $team->initials = 'PF';
        $team->save();

        $team = new Team;
        $team->team_name = 'Rolling Stones';
        $team->initials = 'RS';
        $team->save();

    }
}
