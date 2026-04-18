<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitOfMeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            [
                'name' => 'Kilogram',
                'code' => 'kg',
                'abbreviation' => 'kg',
                'is_active' => true,
            ],
            [
                'name' => 'Liter',
                'code' => 'l',
                'abbreviation' => 'l',
                'is_active' => true,
            ],
            [
                'name' => 'Piece',
                'code' => 'pcs',
                'abbreviation' => 'pcs',
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            \App\Models\Unit::create($item);
        }

    }
}
