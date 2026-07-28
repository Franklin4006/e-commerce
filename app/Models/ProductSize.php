<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSize extends Model
{
    public const SIZES = ['M', 'L', 'XL', 'XXL'];

    protected $fillable = [
        'product_id',
        'product_color_id',
        'size',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        $resync = function (ProductSize $productSize) {
            $total = static::where('product_id', $productSize->product_id)->sum('stock');
            Product::whereKey($productSize->product_id)->update(['stock' => $total]);
        };

        static::saved($resync);
        static::deleted($resync);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }
}
