<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = [
        'name',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * The admin-managed size labels, in display order. Replaces the old
     * hardcoded ProductSize::SIZES constant everywhere it was used.
     *
     * @return array<int, string>
     */
    public static function names(): array
    {
        return static::ordered()->pluck('name')->all();
    }
}
