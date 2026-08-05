<?php

namespace App\Support;

use App\Models\ShippingSlab;
use App\Models\State;

class ShippingCalculator
{
    public static function calculate(?string $stateName, float $orderValue): float
    {
        return static::stateCharge($stateName) + static::slabCharge($orderValue);
    }

    private static function stateCharge(?string $stateName): float
    {
        if (! $stateName) {
            return 0.0;
        }

        return (float) (State::whereRaw('LOWER(name) = ?', [strtolower($stateName)])->value('shipping_charge') ?? 0);
    }

    private static function slabCharge(float $orderValue): float
    {
        $slab = ShippingSlab::where('min_amount', '<=', $orderValue)
            ->where(fn ($query) => $query->whereNull('max_amount')->orWhere('max_amount', '>=', $orderValue))
            ->first();

        return $slab ? (float) $slab->shipping_charge : 0.0;
    }
}
