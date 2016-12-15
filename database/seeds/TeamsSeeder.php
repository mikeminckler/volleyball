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

    }
}
