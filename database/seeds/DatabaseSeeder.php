<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleUserSeeder::class);
        $this->call(TeamsSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
