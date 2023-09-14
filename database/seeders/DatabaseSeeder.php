<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Role;
use App\Models\Team;

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
        $user->password = $this->createPassword();
        $user->save();

        $admin = new Role();
        $admin->name = 'admin';
        $admin->save();

        $user->addRole('admin');

        $team = new Team();
        $team->name = 'Brentwood Sr';
        $team->save();

    }

    protected function createPassword()
    {
        $password = Str::random();

        if (!env('production')) {
            $password = 'password';
        }

        return bcrypt($password);
    }
}
