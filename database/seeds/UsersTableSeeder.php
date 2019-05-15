<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'password'       => '$2y$10$E/iYkUdn1HTvzLakse9z9.t/5lmRCzu.VNbJyobZ.cpNMix7V/BIi',
            ],
            [
                'id'             => 2,
                'name'           => 'User',
                'email'          => 'user@user.com',
                'password'       => '$2y$10$E/iYkUdn1HTvzLakse9z9.t/5lmRCzu.VNbJyobZ.cpNMix7V/BIi',
            ],
        ];

        User::insert($users);
    }
}
