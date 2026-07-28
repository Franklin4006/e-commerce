<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use Database\Seeders\Concerns\GeneratesPlaceholderImages;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    use GeneratesPlaceholderImages;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kurthis = Category::where('name', 'Kurthis')->first();
        $westernWear = Category::where('name', 'Western Wear')->first();

        $banners = [
            [
                'title' => 'New Season Kurthis',
                'sub_title' => 'Fresh prints, ethnic elegance',
                'title_position' => 'bottom-left',
                'button_text' => 'Shop Kurthis',
                'button_url' => $kurthis ? route('category.show', $kurthis) : route('shop.index'),
                'button_color' => '#be185d',
                'color' => '#be185d',
            ],
            [
                'title' => 'The Western Wear Edit',
                'sub_title' => 'Denim jackets, co-ords & jumpsuits',
                'title_position' => 'center',
                'button_text' => 'Explore Now',
                'button_url' => $westernWear ? route('category.show', $westernWear) : route('shop.index'),
                'button_color' => '#8a0f45',
                'color' => '#db2777',
            ],
            [
                'title' => 'Sizes M to XXL, Styled for You',
                'sub_title' => 'Every piece, every size, in stock',
                'title_position' => 'top-right',
                'button_text' => 'Shop All',
                'button_url' => route('shop.index'),
                'button_color' => '#f6a623',
                'color' => '#a21caf',
            ],
        ];

        foreach ($banners as $index => $banner) {
            Banner::updateOrCreate(
                ['title' => $banner['title']],
                [
                    'sub_title' => $banner['sub_title'],
                    'image' => $this->placeholderImage('banners', $banner['title'], $banner['color'], 1600, 640),
                    'title_position' => $banner['title_position'],
                    'button_text' => $banner['button_text'],
                    'button_url' => $banner['button_url'],
                    'button_color' => $banner['button_color'],
                    'priority' => $index,
                    'status' => true,
                ]
            );
        }
    }
}
