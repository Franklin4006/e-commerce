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
            $table->string('size', 8)->nullable()->after('product_id');
            // The old composite unique(user_id, product_id) is the only index
            // backing both the user_id and product_id foreign keys (MySQL
            // never got separate single-column indexes for them). Give
            // product_id its own index, and add the new composite unique
            // (which still has user_id leftmost) before dropping the old one
            // below, so neither FK is ever left without a supporting index.
            $table->index('product_id', 'cart_items_product_id_index');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id', 'size']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_user_id_product_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'product_id', 'size']);
            $table->dropIndex('cart_items_product_id_index');
            $table->dropColumn('size');
        });
    }
};
