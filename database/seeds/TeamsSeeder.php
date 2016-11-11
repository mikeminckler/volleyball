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
        $bcs->save();
            
        $team = new Team;
        $team->team_name = 'Led Zeppelin';
        $team->save();

        $team = new Team;
        $team->team_name = 'Pink Floyd';
        $team->save();

        $team = new Team;
        $team->team_name = 'Rolling Stones';
        $team->save();

    }
}
