<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\Size;
use Database\Seeders\Concerns\GeneratesPlaceholderImages;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use GeneratesPlaceholderImages;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productsByCategory = [
            'Kurthis' => [
                ['name' => 'Floral Print Anarkali Kurti', 'price' => 899, 'mrp' => 1299, 'color' => '#be185d'],
                ['name' => 'Cotton Straight Kurti', 'price' => 649, 'mrp' => 899, 'color' => '#db2777'],
                ['name' => 'Embroidered A-Line Kurti', 'price' => 1099, 'mrp' => 1599, 'color' => '#9d174d'],
                ['name' => 'Chikankari Kurti', 'price' => 1299, 'mrp' => 1799, 'color' => '#be185d'],
                ['name' => 'Rayon Printed Straight Kurti', 'price' => 749, 'mrp' => 999, 'color' => '#db2777'],
                ['name' => 'Bandhani Print Kurti', 'price' => 799, 'mrp' => 1099, 'color' => '#9d174d'],
                ['name' => 'Angrakha Style Kurti', 'price' => 999, 'mrp' => 1399, 'color' => '#be185d'],
                ['name' => 'Mirror Work Kurti', 'price' => 1199, 'mrp' => 1699, 'color' => '#db2777'],
                ['name' => 'Kalamkari Print Kurti', 'price' => 849, 'mrp' => 1199, 'color' => '#9d174d'],
                ['name' => 'High-Low Hem Kurti', 'price' => 949, 'mrp' => 1349, 'color' => '#be185d'],
            ],
            'Tops' => [
                ['name' => 'Casual Cotton Top', 'price' => 499, 'mrp' => 699, 'color' => '#db2777'],
                ['name' => 'Off-Shoulder Top', 'price' => 599, 'mrp' => 849, 'color' => '#ec4899'],
                ['name' => 'Printed Crop Top', 'price' => 449, 'mrp' => 599, 'color' => '#db2777'],
                ['name' => 'Sleeveless Ruffle Top', 'price' => 549, 'mrp' => 749, 'color' => '#ec4899'],
                ['name' => 'Puff Sleeve Top', 'price' => 599, 'mrp' => 849, 'color' => '#ec4899'],
                ['name' => 'Wrap Top', 'price' => 649, 'mrp' => 899, 'color' => '#db2777'],
                ['name' => 'Peplum Top', 'price' => 699, 'mrp' => 999, 'color' => '#ec4899'],
                ['name' => 'Tie-Front Top', 'price' => 549, 'mrp' => 749, 'color' => '#db2777'],
            ],
            'Leggings' => [
                ['name' => 'Ankle-Length Leggings', 'price' => 349, 'mrp' => 499, 'color' => '#a21caf'],
                ['name' => 'Printed Leggings', 'price' => 399, 'mrp' => 549, 'color' => '#a21caf'],
                ['name' => 'Churidar Leggings', 'price' => 329, 'mrp' => 449, 'color' => '#86198f'],
                ['name' => 'Cotton Stretch Leggings', 'price' => 299, 'mrp' => 399, 'color' => '#a21caf'],
                ['name' => 'High-Waist Leggings', 'price' => 379, 'mrp' => 529, 'color' => '#86198f'],
                ['name' => 'Solid Color Leggings', 'price' => 299, 'mrp' => 399, 'color' => '#a21caf'],
                ['name' => 'Patiala Style Leggings', 'price' => 449, 'mrp' => 599, 'color' => '#86198f'],
                ['name' => 'Jeggings', 'price' => 499, 'mrp' => 699, 'color' => '#a21caf'],
            ],
            'Western Wear' => [
                ['name' => 'Denim Jacket', 'price' => 1499, 'mrp' => 1999, 'color' => '#ec4899'],
                ['name' => 'Palazzo Set', 'price' => 1199, 'mrp' => 1699, 'color' => '#db2777'],
                ['name' => 'Co-ord Set', 'price' => 1399, 'mrp' => 1899, 'color' => '#ec4899'],
                ['name' => 'Jumpsuit', 'price' => 1299, 'mrp' => 1799, 'color' => '#db2777'],
                ['name' => 'Bodycon Dress', 'price' => 1399, 'mrp' => 1899, 'color' => '#db2777'],
                ['name' => 'A-Line Skater Dress', 'price' => 1299, 'mrp' => 1799, 'color' => '#ec4899'],
                ['name' => 'Wide-Leg Trousers', 'price' => 999, 'mrp' => 1399, 'color' => '#db2777'],
                ['name' => 'Blazer Set', 'price' => 1799, 'mrp' => 2399, 'color' => '#ec4899'],
            ],
        ];

        $sizeNames = Size::names();
        $palette = Color::ordered()->get();
        $paletteSize = $palette->count();

        $priority = 0;
        $productIndex = 0;

        foreach ($productsByCategory as $categoryName => $products) {
            $category = Category::where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            foreach ($products as $item) {
                $product = Product::updateOrCreate(
                    ['name' => $item['name']],
                    [
                        'category_id' => $category->id,
                        'description' => "The {$item['name']} offers great quality and value, perfect for everyday wear. Available in sizes M to XXL.",
                        'mrp' => $item['mrp'],
                        'sale_price' => $item['price'],
                        'thumbnail' => $this->placeholderImage('products', $item['name'], $item['color'], 800, 800),
                        'priority' => $priority++,
                        'status' => true,
                    ]
                );

                // Two rotating colors per product, picked from the
                // admin-managed color palette, each a fully purchasable
                // variant with its own photo and its own stock by size. The
                // colorless "Stock by Size" pool is zeroed out below since a
                // product with colors is only ever bought through them.
                $colorDefs = [
                    $palette[$productIndex % $paletteSize],
                    $palette[($productIndex + 1) % $paletteSize],
                ];
                $productIndex++;

                foreach ($sizeNames as $size) {
                    ProductSize::updateOrCreate(
                        ['product_id' => $product->id, 'product_color_id' => null, 'size' => $size],
                        ['stock' => 0]
                    );
                }

                foreach ($colorDefs as $colorSort => $colorDef) {
                    $color = ProductColor::updateOrCreate(
                        ['product_id' => $product->id, 'name' => $colorDef->name],
                        [
                            'color_id' => $colorDef->id,
                            'hex' => $colorDef->hex,
                            'sort_order' => $colorSort,
                        ]
                    );

                    // Two-photo gallery per color, so the storefront gallery
                    // has more than one thumbnail to re-render on selection.
                    if ($color->images()->count() === 0) {
                        foreach (range(1, 2) as $i) {
                            $color->images()->create([
                                'image' => $this->placeholderImage('products/colors', "{$item['name']} - {$colorDef->name} {$i}", $colorDef->hex, 800, 800),
                                'sort_order' => $i - 1,
                            ]);
                        }
                    }

                    foreach ($sizeNames as $size) {
                        ProductSize::updateOrCreate(
                            ['product_id' => $product->id, 'product_color_id' => $color->id, 'size' => $size],
                            ['stock' => rand(0, 20)]
                        );
                    }
                }
            }
        }
    }
}
