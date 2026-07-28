<?php

namespace Database\Seeders;

use App\Models\Category;
use Database\Seeders\Concerns\GeneratesPlaceholderImages;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use GeneratesPlaceholderImages;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Kurthis', 'color' => '#be185d'],
            ['name' => 'Tops', 'color' => '#db2777'],
            ['name' => 'Leggings', 'color' => '#a21caf'],
            ['name' => 'Western Wear', 'color' => '#ec4899'],
        ];

        foreach ($categories as $index => $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'image' => $this->placeholderImage('categories', $category['name'], $category['color'], 600, 600),
                    'status' => true,
                    'priority' => $index,
                ]
            );
        }
    }
}
