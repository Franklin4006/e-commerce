<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = ['M', 'L', 'XL', 'XXL'];

        foreach ($sizes as $index => $name) {
            Size::updateOrCreate(['name' => $name], ['sort_order' => $index]);
        }
    }
}
