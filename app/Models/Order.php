<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    public const STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    public const PAYMENT_STATUSES = ['pending', 'paid', 'failed'];

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'razorpay_order_id',
        'razorpay_payment_id',
        'transaction_reference',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_name',
        'shipping_phone',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'billing_name',
        'billing_phone',
        'billing_address_line1',
        'billing_address_line2',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'total_mrp',
        'total_discount',
        'subtotal',
        'shipping_charge',
        'coupon_code',
        'coupon_discount',
        'grand_total',
    ];

    protected function casts(): array
    {
        return [
            'total_mrp' => 'decimal:2',
            'total_discount' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'shipping_charge' => 'decimal:2',
            'coupon_discount' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    /**
     * Resolve route-model bindings by the human-readable order number and
     * generate order URLs with it (e.g. /orders/ORD20260714JHXDS4) instead
     * of the numeric primary key.
     */
    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD'.now()->format('Ymd').strtoupper(Str::random(6));
        } while (static::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    /**
     * Orders that count as an actual, confirmed sale: paid, or COD (which is
     * always a firm order even before cash is collected). Excludes online
     * payments still pending or failed, which only exist as an initiation
     * record until they're confirmed — those live on the Payment Issues page
     * instead of the main order list, revenue totals, and order counts.
     */
    public function scopePaymentConfirmed(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q->where('payment_status', 'paid')->orWhere('payment_method', 'cod'));
    }

    public static function badgeClassForStatus(string $status): string
    {
        return match ($status) {
            'delivered' => 'badge-success',
            'processing', 'shipped' => 'badge-info',
            'cancelled' => 'badge-danger',
            default => 'badge-muted',
        };
    }

    public static function badgeClassForPaymentStatus(string $paymentStatus): string
    {
        return match ($paymentStatus) {
            'paid' => 'badge-success',
            'failed' => 'badge-danger',
            default => 'badge-warning',
        };
    }
}
