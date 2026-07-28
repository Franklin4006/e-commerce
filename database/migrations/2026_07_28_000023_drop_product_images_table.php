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
        // Per-product image galleries are replaced by per-color galleries
        // (see product_color_images). Existing files on disk are left alone;
        // only this table (and the admin UI/model referencing it) go away.
        Schema::dropIfExists('product_images');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image');
            $table->timestamps();
        });
    }
};
