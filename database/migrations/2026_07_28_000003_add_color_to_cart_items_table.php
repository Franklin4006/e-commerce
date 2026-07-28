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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('product_color_id')->nullable()->after('size')->constrained()->nullOnDelete();
        });

        // Add the new composite unique (still user_id-leftmost, so it keeps
        // backing the user_id FK) before dropping the old one, so user_id's
        // FK is never left without a supporting index.
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id', 'product_color_id', 'size']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_user_id_product_id_size_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id', 'size']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'product_id', 'product_color_id', 'size']);
            $table->dropConstrainedForeignId('product_color_id');
        });
    }
};
