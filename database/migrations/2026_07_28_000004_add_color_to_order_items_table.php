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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_color_id')->nullable()->after('size')->constrained()->nullOnDelete();
            // Denormalized like product_name, so the order still shows the
            // color that was purchased even if the color is later renamed
            // or removed from the product.
            $table->string('color_name')->nullable()->after('product_color_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_color_id');
            $table->dropColumn('color_name');
        });
    }
};
