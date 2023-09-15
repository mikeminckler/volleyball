<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use App\Models\Stat;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = new User;
        $user->name = 'Mike Minckler';
        $user->email = 'mike.minckler@brentwood.ca';
        $user->email_verified_at = now();
        $user->password = bcrypt('volleyball');
        $user->save();

        $admin = new Role();
        $admin->name = 'admin';
        $admin->save();

        $user->addRole('admin');

        $team = new Team();
        $team->name = 'Brentwood Sr';
        $team->save();

        $stats = collect([
            ['name' => 'Passing', 'high_score' => 3, 'low_score' => 0],
            ['name' => 'Serving', 'high_score' => 0, 'low_score' => 4],
            ['name' => 'Hitting', 'high_score' => 1, 'low_score' => -1],
            ['name' => 'Blocking', 'high_score' => 1, 'low_score' => 1],
            //['name' => 'Setting', 'high_score' => 1, 'low_score' => -1],
        ]);

        foreach ($stats as $stat_data) {
            $stat = new Stat();
            $stat->name = Arr::get($stat_data, 'name');
            $stat->high_score = Arr::get($stat_data, 'high_score');
            $stat->low_score = Arr::get($stat_data, 'low_score');
            $stat->save();
        }
    }

}
