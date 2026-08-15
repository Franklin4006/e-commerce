<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #111827; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-table td { vertical-align: top; }
        .company-name { font-size: 18px; font-weight: bold; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        .invoice-title { font-size: 20px; font-weight: bold; text-align: right; margin: 0 0 4px; }
        .text-right { text-align: right; }
        .address-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .address-table td { width: 50%; vertical-align: top; padding-right: 20px; }
        .address-heading { font-size: 11px; text-transform: uppercase; color: #6b7280; margin: 0 0 4px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th, table.items td { border: 1px solid #e5e7eb; padding: 6px 8px; font-size: 11px; }
        table.items th { background: #f3f4f6; text-align: left; }
        table.totals { width: 40%; margin-left: 60%; border-collapse: collapse; }
        table.totals td { padding: 4px 8px; font-size: 12px; }
        table.totals .grand-total td { font-weight: bold; font-size: 14px; border-top: 2px solid #111827; padding-top: 8px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; background: #f3f4f6; }
        .footer { margin-top: 30px; text-align: center; color: #6b7280; font-size: 11px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                @if (! empty($siteSettings['logo']))
                    <img src="{{ public_path('uploads/'.$siteSettings['logo']) }}" alt="{{ $siteSettings['site_name'] ?? config('app.name') }}" style="max-height: 50px; margin-bottom: 8px;"><br>
                @else
                    <p class="company-name">{{ $siteSettings['site_name'] ?? config('app.name') }}</p>
                @endif
                @if (! empty($siteSettings['address']))
                    <p class="muted">{{ $siteSettings['address'] }}</p>
                @endif
                @if (! empty($siteSettings['email']))
                    <p class="muted">{{ $siteSettings['email'] }}@if (! empty($siteSettings['phone'])) &middot; {{ $siteSettings['phone'] }}@endif</p>
                @endif
            </td>
            <td class="text-right">
                <p class="invoice-title">INVOICE</p>
                <p class="muted">Order #: {{ $order->order_number }}</p>
                <p class="muted">Date: {{ $order->created_at->format('d M Y') }}</p>
                <p><span class="badge">{{ ucfirst($order->status) }}</span></p>
            </td>
        </tr>
    </table>

    <table class="address-table">
        <tr>
            <td>
                <p class="address-heading">Billed To</p>
                <p>
                    {{ $order->billing_name }}<br>
                    {{ $order->billing_phone }}<br>
                    {{ $order->billing_address_line1 }}@if ($order->billing_address_line2), {{ $order->billing_address_line2 }}@endif<br>
                    {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postal_code }}<br>
                    {{ $order->billing_country }}
                </p>
            </td>
            <td>
                <p class="address-heading">Shipped To</p>
                <p>
                    {{ $order->shipping_name }}<br>
                    {{ $order->shipping_phone }}<br>
                    {{ $order->shipping_address_line1 }}@if ($order->shipping_address_line2), {{ $order->shipping_address_line2 }}@endif<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                    {{ $order->shipping_country }}
                </p>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if ($item->size || $item->color_name)
                            <br><span class="muted">
                                @if ($item->color_name) {{ $item->color_name }} @endif
                                @if ($item->size) @if ($item->color_name) &middot; @endif Size {{ $item->size }} @endif
                            </span>
                        @endif
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->sale_price, 2) }}</td>
                    <td class="text-right">₹{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Total MRP Price</td>
            <td class="text-right">₹{{ number_format($order->total_mrp, 2) }}</td>
        </tr>
        <tr>
            <td>Total Discounted Price</td>
            <td class="text-right">₹{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if ($order->total_discount > 0)
            <tr>
                <td>Total Save Amount</td>
                <td class="text-right">&minus; ₹{{ number_format($order->total_discount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td>Shipping Charge</td>
            <td class="text-right">{{ $order->shipping_charge > 0 ? '₹'.number_format($order->shipping_charge, 2) : 'FREE' }}</td>
        </tr>
        @if ($gstPercentage !== null)
            <tr>
                <td>Taxable Value</td>
                <td class="text-right">₹{{ number_format($taxableValue, 2) }}</td>
            </tr>
            <tr>
                <td>GST ({{ rtrim(rtrim(number_format($gstPercentage, 2), '0'), '.') }}%) (Included)</td>
                <td class="text-right">₹{{ number_format($gstAmount, 2) }}</td>
            </tr>
        @endif
        <tr class="grand-total">
            <td>Total Amount</td>
            <td class="text-right">₹{{ number_format($order->grand_total, 2) }}</td>
        </tr>
    </table>

    @if ($gstPercentage !== null)
        <p class="muted">Prices are inclusive of GST.</p>
    @endif

    <p class="muted">Payment Method: {{ strtoupper($order->payment_method) }} &middot; Payment Status: {{ ucfirst($order->payment_status) }}</p>

    <div class="footer">
        <p>Thank you for shopping with {{ $siteSettings['site_name'] ?? config('app.name') }}!</p>
    </div>
</body>
</html>
