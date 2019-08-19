<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();

        $user->username = 'admin';
        $user->email    = 'admin@app.com';
        $user->password = Hash::make('secret');
        $user->save();
    }
}
