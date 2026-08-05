<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSlab extends Model
{
    protected $fillable = [
        'min_amount',
        'max_amount',
        'shipping_charge',
    ];

    protected function casts(): array
    {
        return [
            'min_amount' => 'decimal:2',
            'max_amount' => 'decimal:2',
            'shipping_charge' => 'decimal:2',
        ];
    }
}
