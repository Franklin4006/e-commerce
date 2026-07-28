<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SettingSeeder::class,
            EmailTemplateSeeder::class,
            CategorySeeder::class,
            SizeSeeder::class,
            ColorSeeder::class,
            ProductSeeder::class,
            BannerSeeder::class,
            FaqSeeder::class,
            AboutUsSeeder::class,
            LegalContentSeeder::class,
        ]);
    }
}
