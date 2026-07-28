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
        Schema::table('product_colors', function (Blueprint $table) {
            // Nullable + nullOnDelete: product_colors.name/hex stay the
            // source of truth for display everywhere (cart, PDP, orders),
            // this just links back to the admin-managed master palette so
            // the product form can offer a "pick a color" dropdown instead
            // of free-typing a name + hex per product. Existing rows (typed
            // before this column existed) are backfilled by ColorSeeder.
            $table->foreignId('color_id')->nullable()->after('product_id')->constrained('colors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_colors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('color_id');
        });
    }
};
