<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Move each color's single existing photo (product_colors.image) into
        // the new per-color gallery table as its first image, before that
        // column is dropped in the next migration.
        $rows = DB::table('product_colors')->whereNotNull('image')->get(['id', 'image', 'created_at', 'updated_at']);

        foreach ($rows as $row) {
            DB::table('product_color_images')->insert([
                'product_color_id' => $row->id,
                'image' => $row->image,
                'sort_order' => 0,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: the data now lives in product_color_images and is left
        // there; product_colors.image is restored (empty) by the previous
        // migration's down() only if it's rolled back too.
    }
};
