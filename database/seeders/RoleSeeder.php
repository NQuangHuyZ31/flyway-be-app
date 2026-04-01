<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            ['name' => 'Admin', 'description' => 'Quản trị viên', 'is_active' => true],
            ['name' => 'Manager', 'description' => 'Quản lí', 'is_active' => true],
            ['name' => 'Employee', 'description' => 'Nhân viên', 'is_active' => true],
        ];

        foreach($data as $role) {
            \App\Models\Role::create($role);
        }
    }
}
