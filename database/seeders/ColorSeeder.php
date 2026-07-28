<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\ProductColor;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $palette = [
            ['name' => 'Maroon', 'hex' => '#7f1d1d'],
            ['name' => 'Navy Blue', 'hex' => '#1e3a5f'],
            ['name' => 'Mustard Yellow', 'hex' => '#b45309'],
            ['name' => 'Emerald Green', 'hex' => '#065f46'],
            ['name' => 'Blush Pink', 'hex' => '#db2777'],
            ['name' => 'Charcoal Black', 'hex' => '#1f2937'],
        ];

        $colorsByName = [];

        foreach ($palette as $index => $item) {
            $colorsByName[$item['name']] = Color::updateOrCreate(
                ['name' => $item['name']],
                ['hex' => $item['hex'], 'sort_order' => $index]
            );
        }

        // Backfill product_colors created before this master table existed
        // (they already have the matching name typed in directly) so the
        // admin product form's color picker shows them pre-selected.
        ProductColor::whereNull('color_id')
            ->whereIn('name', array_keys($colorsByName))
            ->get()
            ->each(fn (ProductColor $productColor) => $productColor->update([
                'color_id' => $colorsByName[$productColor->name]->id,
            ]));
    }
}
