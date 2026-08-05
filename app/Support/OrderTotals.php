<?php

namespace App\Support;

use App\Models\Address;
use App\Models\Coupon;

class OrderTotals
{
    /**
     * @return array{totalMrp: float, total: float, savings: float, shippingCharge: float, couponCode: ?string, couponDiscount: float, grandTotal: float}
     */
    public static function forCart(?Address $shippingAddress = null, ?Coupon $coupon = null): array
    {
        $totalMrp = Cart::totalMrp();
        $total = Cart::total();
        $savings = Cart::savings();

        return static::withShipping($totalMrp, $total, $savings, $shippingAddress, $coupon);
    }

    /**
     * @param  array<int, array{mrp: float, sale_price: float, quantity: int, subtotal: float}>  $itemsData
     * @return array{totalMrp: float, total: float, savings: float, shippingCharge: float, couponCode: ?string, couponDiscount: float, grandTotal: float}
     */
    public static function forItems(array $itemsData, ?Address $shippingAddress = null, ?Coupon $coupon = null): array
    {
        $totalMrp = 0.0;
        $total = 0.0;

        foreach ($itemsData as $item) {
            $totalMrp += $item['mrp'] * $item['quantity'];
            $total += $item['subtotal'];
        }

        $savings = $totalMrp - $total;

        return static::withShipping($totalMrp, $total, $savings, $shippingAddress, $coupon);
    }

    /**
     * @return array{totalMrp: float, total: float, savings: float, shippingCharge: float, couponCode: ?string, couponDiscount: float, grandTotal: float}
     */
    private static function withShipping(float $totalMrp, float $total, float $savings, ?Address $shippingAddress = null, ?Coupon $coupon = null): array
    {
        $shippingCharge = ShippingCalculator::calculate($shippingAddress?->state, $total);

        $couponCode = null;
        $couponDiscount = 0.0;

        if ($coupon !== null) {
            $couponCode = $coupon->code;
            $couponDiscount = $coupon->calculateDiscount($total);
        }

        $grandTotal = $total - $couponDiscount + $shippingCharge;

        return compact('totalMrp', 'total', 'savings', 'shippingCharge', 'couponCode', 'couponDiscount', 'grandTotal');
    }
}
