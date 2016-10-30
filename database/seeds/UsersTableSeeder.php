<?php

use Illuminate\Database\Seeder;

use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mike = new User;
        $mike->first_name = 'Mike';
        $mike->last_name = 'Minckler';
        $mike->email = 'mikeminckler@gmail.com';
        $mike->password = bcrypt('password');
        $mike->save();
    }
}
