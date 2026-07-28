<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ProductColor extends Model
{
    protected $fillable = [
        'product_id',
        'color_id',
        'name',
        'hex',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // The DB-level cascadeOnDelete on product_sizes/product_color_images
        // removes child rows with a single bulk SQL statement, which never
        // fires their model events — so ProductSize's stock resync onto
        // Product.stock would silently never run, and image files would be
        // orphaned on disk. Deleting each row through Eloquent here fires
        // those events / storage cleanup properly.
        static::deleting(function (ProductColor $productColor) {
            $productColor->sizes->each->delete();

            $productColor->images->each(function (ProductColorImage $image) {
                Storage::disk('public')->delete($image->image);
                $image->delete();
            });
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function paletteColor(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductColorImage::class)->orderBy('sort_order');
    }

    public function coverImage(): ?string
    {
        return $this->images->first()?->image;
    }
}
