<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->foreignId('product_color_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
        });

        // The old unique(product_id, size) is the only index backing the
        // product_id foreign key. Give product_id its own index, add the new
        // composite unique, then drop the old one — so the FK is never left
        // without a supporting index (MySQL refuses to drop it otherwise).
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->index('product_id', 'product_sizes_product_id_index');
        });

        Schema::table('product_sizes', function (Blueprint $table) {
            // Stock now lives per (product, color, size). Colorless products keep
            // product_color_id null, so this behaves exactly like the old
            // (product_id, size) unique for them.
            $table->unique(['product_id', 'product_color_id', 'size']);
        });

        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->unique(['product_id', 'size']);
        });

        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'product_color_id', 'size']);
            $table->dropIndex('product_sizes_product_id_index');
            $table->dropConstrainedForeignId('product_color_id');
        });
    }
};
