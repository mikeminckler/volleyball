<?php

use Illuminate\Database\Seeder;

use App\Stat;

class StatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stat = new Stat;
        $stat->stat_name = 'Passing';
        $stat->save();

        $stat = new Stat;
        $stat->stat_name = 'Serving';
        $stat->save();

        $stat = new Stat;
        $stat->stat_name = 'Hitting';
        $stat->save();

        $stat = new Stat;
        $stat->stat_name = 'Blocking';
        $stat->save();
    }
}
