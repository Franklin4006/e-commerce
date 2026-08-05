<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = [
        'name',
        'shipping_charge',
    ];

    protected function casts(): array
    {
        return [
            'shipping_charge' => 'decimal:2',
        ];
    }
}
