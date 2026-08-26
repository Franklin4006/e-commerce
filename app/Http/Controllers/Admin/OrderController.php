<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Size;
use App\Models\State;
use App\Models\StockMovement;
use App\Models\User;
use App\Support\Invoice;
use App\Support\OrderCreator;
use App\Support\OrderTotals;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with('user')
            ->when($request->filled('search'), fn ($query) => $query->where('order_number', 'like', '%'.$request->input('search').'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('items', 'statusHistories.changedBy');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $statusChanged = $order->status !== $validated['status'];

        $this->applyStatusTransition($order, $validated['status'], $validated['note'] ?? null, $request->user()->id);

        if ($statusChanged) {
            OrderCreator::sendStatusUpdateEmail($order);
        }

        return back()->with('status', 'Order status updated successfully.');
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['integer', 'exists:orders,id'],
            'status' => ['required', Rule::in(Order::STATUSES)],
        ]);

        $orders = Order::with('items')->whereIn('id', $validated['order_ids'])->get();

        foreach ($orders as $order) {
            $statusChanged = $order->status !== $validated['status'];

            $this->applyStatusTransition($order, $validated['status'], null, $request->user()->id);

            if ($statusChanged) {
                OrderCreator::sendStatusUpdateEmail($order);
            }
        }

        return back()->with('status', $orders->count().' order(s) updated to "'.ucfirst($validated['status']).'".');
    }

    private function applyStatusTransition(Order $order, string $status, ?string $note, int $changedByUserId): void
    {
        DB::transaction(function () use ($order, $status, $note, $changedByUserId) {
            if ($status === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    if (! $item->product_id || ! $item->size) {
                        continue;
                    }

                    $sizeRow = ProductSize::where('product_id', $item->product_id)
                        ->where('product_color_id', $item->product_color_id)
                        ->where('size', $item->size)
                        ->lockForUpdate()
                        ->first();

                    if (! $sizeRow) {
                        continue;
                    }

                    $sizeRow->increment('stock', $item->quantity);

                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'size' => $item->size,
                        'product_color_id' => $item->product_color_id,
                        'order_id' => $order->id,
                        'changed_by' => $changedByUserId,
                        'quantity_change' => $item->quantity,
                        'stock_after' => $sizeRow->stock,
                        'reason' => 'Order cancelled',
                    ]);
                }
            }

            $order->update(['status' => $status]);

            $order->statusHistories()->create([
                'status' => $status,
                'note' => $note,
                'changed_by' => $changedByUserId,
            ]);
        });
    }

    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        if ($order->payment_method !== 'cod') {
            abort(403, 'Payment status can only be changed manually for COD orders.');
        }

        $validated = $request->validate([
            'payment_status' => ['required', Rule::in(Order::PAYMENT_STATUSES)],
        ]);

        $order->update(['payment_status' => $validated['payment_status']]);

        return back()->with('status', 'Payment status updated successfully.');
    }

    public function invoice(Order $order): Response
    {
        return Invoice::forOrder($order)->download(Invoice::filename($order));
    }

    public function create(Request $request): View
    {
        $customer = null;
        $states = State::orderBy('name')->get();

        if ($request->filled('customer_id')) {
            $customer = User::where('is_admin', false)->find($request->input('customer_id'));
        }

        if (! $customer) {
            $customers = User::where('is_admin', false)->orderBy('name')->get();

            return view('admin.orders.create', ['customer' => null, 'customers' => $customers, 'products' => collect(), 'states' => $states]);
        }

        $customer->load(['addresses' => fn ($query) => $query->orderByDesc('is_default')]);
        $products = Product::with('colors')->where('status', true)->orderBy('name')->get();

        return view('admin.orders.create', ['customer' => $customer, 'customers' => collect(), 'products' => $products, 'states' => $states]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'integer', 'exists:products,id'],
            'size' => ['required', 'array', 'min:1'],
            'size.*' => ['required', Rule::in(Size::names())],
            'color_id' => ['nullable', 'array'],
            'color_id.*' => ['nullable', 'integer'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:cod,manual'],
            'shipping_address_id' => ['nullable', 'integer'],
            'billing_address_id' => ['nullable', 'integer'],
            'new_shipping_label' => ['required_without:shipping_address_id', 'nullable', 'string', 'max:255'],
            'new_shipping_name' => ['required_without:shipping_address_id', 'nullable', 'string', 'max:255'],
            'new_shipping_phone' => ['required_without:shipping_address_id', 'nullable', 'string', 'max:30'],
            'new_shipping_address_line1' => ['required_without:shipping_address_id', 'nullable', 'string', 'max:255'],
            'new_shipping_address_line2' => ['nullable', 'string', 'max:255'],
            'new_shipping_city' => ['required_without:shipping_address_id', 'nullable', 'string', 'max:255'],
            'new_shipping_state' => ['required_without:shipping_address_id', 'nullable', 'string', Rule::exists('states', 'name')],
            'new_shipping_postal_code' => ['required_without:shipping_address_id', 'nullable', 'string', 'max:20'],
            'new_shipping_country' => ['required_without:shipping_address_id', 'nullable', 'string', 'max:255'],
        ]);

        $customer = User::where('is_admin', false)->find($validated['customer_id']);
        abort_if(! $customer, 404);

        if ($request->filled('shipping_address_id')) {
            $shippingAddress = $customer->addresses()->find($request->input('shipping_address_id'));
            abort_if(! $shippingAddress, 404);
        } else {
            $shippingAddress = $customer->addresses()->create([
                'label' => $validated['new_shipping_label'],
                'name' => $validated['new_shipping_name'],
                'phone' => $validated['new_shipping_phone'],
                'address_line1' => $validated['new_shipping_address_line1'],
                'address_line2' => $validated['new_shipping_address_line2'] ?? null,
                'city' => $validated['new_shipping_city'],
                'state' => $validated['new_shipping_state'],
                'postal_code' => $validated['new_shipping_postal_code'],
                'country' => $validated['new_shipping_country'],
                'is_default' => $customer->addresses()->count() === 0,
            ]);
        }

        $billingSameAsShipping = $request->boolean('billing_same_as_shipping', true);

        if ($billingSameAsShipping) {
            $billingAddress = $shippingAddress;
        } elseif ($request->filled('billing_address_id')) {
            $billingAddress = $customer->addresses()->find($request->input('billing_address_id'));
            abort_if(! $billingAddress, 404);
        } else {
            $newBilling = $request->validate([
                'new_billing_label' => ['required', 'string', 'max:255'],
                'new_billing_name' => ['required', 'string', 'max:255'],
                'new_billing_phone' => ['required', 'string', 'max:30'],
                'new_billing_address_line1' => ['required', 'string', 'max:255'],
                'new_billing_address_line2' => ['nullable', 'string', 'max:255'],
                'new_billing_city' => ['required', 'string', 'max:255'],
                'new_billing_state' => ['required', 'string', Rule::exists('states', 'name')],
                'new_billing_postal_code' => ['required', 'string', 'max:20'],
                'new_billing_country' => ['required', 'string', 'max:255'],
            ]);

            $billingAddress = $customer->addresses()->create([
                'label' => $newBilling['new_billing_label'],
                'name' => $newBilling['new_billing_name'],
                'phone' => $newBilling['new_billing_phone'],
                'address_line1' => $newBilling['new_billing_address_line1'],
                'address_line2' => $newBilling['new_billing_address_line2'] ?? null,
                'city' => $newBilling['new_billing_city'],
                'state' => $newBilling['new_billing_state'],
                'postal_code' => $newBilling['new_billing_postal_code'],
                'country' => $newBilling['new_billing_country'],
                'is_default' => false,
            ]);
        }

        $pairs = collect($validated['product_id'])->map(fn ($productId, $i) => [
            'product_id' => $productId,
            'size' => $validated['size'][$i],
            'color_id' => $validated['color_id'][$i] ?? null,
            'quantity' => $validated['quantity'][$i],
        ])->values()->all();

        try {
            $itemsData = OrderCreator::snapshotItemsFromInput($pairs);
            $totals = OrderTotals::forItems($itemsData, $shippingAddress);
            $paymentStatus = $validated['payment_method'] === 'manual' ? 'paid' : 'pending';

            $order = OrderCreator::create($customer, $itemsData, $shippingAddress, $billingAddress, $totals, $validated['payment_method'], $paymentStatus);
        } catch (RuntimeException $e) {
            return back()->withErrors(['products' => $e->getMessage()])->withInput();
        }

        OrderCreator::sendConfirmationEmail($customer, $order);

        return redirect()->route('admin.orders.show', $order)->with('status', 'Order created successfully.');
    }
}
