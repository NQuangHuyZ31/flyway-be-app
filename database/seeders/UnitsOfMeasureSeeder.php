<?php

namespace Database\Seeders;

use App\Models\UnitsOfMeasure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitsOfMeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $data = [
            [
                'name' => 'Cái',
                'code' => 'PCS',
                'abbreviation' => 'Cái',
                'description' => 'Đơn vị tính theo cái',
                'conversion_factor' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Hộp',
                'code' => 'BOX',
                'abbreviation' => 'Hộp',
                'description' => 'Đơn vị tính theo hộp',
                'conversion_factor' => 10,
                'is_active' => true
            ],
            [
                'name' => 'Mét',
                'code' => 'M',
                'abbreviation' => 'M',
                'description' => 'Đơn vị tính theo mét',
                'conversion_factor' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Kg',
                'code' => 'KG',
                'abbreviation' => 'Kg',
                'description' => 'Đơn vị tính theo kilogram',
                'conversion_factor' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Gram',
                'code' => 'G',
                'abbreviation' => 'G',
                'description' => 'Đơn vị tính theo gram',
                'conversion_factor' => 0.001,
                'is_active' => true
            ]
        ];

        foreach ($data as $item) {
            UnitsOfMeasure::create($item);
        }
    }
}
