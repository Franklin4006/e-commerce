<?php

namespace Database\Seeders;

use App\Models\Setting;
use Database\Seeders\Concerns\GeneratesPlaceholderImages;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    use GeneratesPlaceholderImages;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $logo = $this->placeholderImage('settings', 'LOGO', '#be185d', 320, 100);

        $values = [
            'site_name' => 'Jana Boutique',
            'logo' => $logo,
            'meta_title' => 'Jana Boutique - Women\'s Exclusive Fashion Online',
            'meta_description' => 'Shop Kurthis, Tops, Leggings and Western Wear at Jana Boutique. Sizes M to XXL, fast delivery, secure payments, easy returns.',
            'meta_keywords' => 'jana boutique, womens fashion, kurthis, tops, leggings, western wear',
            'primary_color' => '#be185d',
            'secondary_color' => '#f6a623',
            'email' => 'support@janaboutique.test',
            'phone' => '+91 98765 43210',
            'address' => '42, Anna Salai, T. Nagar, Chennai, Tamil Nadu 600017, India',
            'social_facebook' => 'https://facebook.com/janaboutique',
            'social_instagram' => 'https://instagram.com/janaboutique',
            'social_twitter' => 'https://x.com/janaboutique',
            'social_youtube' => 'https://youtube.com/@janaboutique',
        ];

        foreach ($values as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
