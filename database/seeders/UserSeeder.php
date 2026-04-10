<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123456'),
                'birthday' => '1990-01-01',
                'department' => 'HR',
                'avatar' => null,
                'phone' => '0123456789',
                'province' => 'Hanoi',
                'ward' => 'Ward 1',
                'address' => '123 Main Street',
                'status' => 1

            ],
            [
                'name' => 'manager',
                'email' => 'manager@gmail.com',
                'password' => Hash::make('123456'),
                'birthday' => '1990-01-01',
                'department' => 'Engineering',
                'avatar' => null,
                'phone' => '0123456789',
                'province' => 'Hanoi',
                'ward' => 'Ward 2',
                'address' => '456 Main Street',
                'status' => 1
            ]
        ];

        foreach($data as $user) {
            User::create($user);
        }
    }
}
