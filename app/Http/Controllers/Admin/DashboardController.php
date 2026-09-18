<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const PERIODS = [
        'this_week' => 'This Week',
        'this_month' => 'This Month',
        'last_3_months' => 'Last 3 Months',
        'last_6_months' => 'Last 6 Months',
        'this_fy' => 'This Financial Year',
        'custom' => 'Custom Range',
    ];

    public function index(Request $request): View
    {
        $period = array_key_exists($request->input('period'), self::PERIODS) ? $request->input('period') : 'this_month';
        $paymentMethod = in_array($request->input('payment_method'), Order::PAYMENT_METHODS, true) ? $request->input('payment_method') : null;

        [$start, $end] = $this->resolvePeriod($period, $request->input('from'), $request->input('to'));

        $revenueTotal = (float) Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->paymentConfirmed()
            ->when($paymentMethod, fn ($query) => $query->where('payment_method', $paymentMethod))
            ->sum('grand_total');

        $orderCount = Order::whereBetween('created_at', [$start, $end])
            ->paymentConfirmed()
            ->when($paymentMethod, fn ($query) => $query->where('payment_method', $paymentMethod))
            ->count();

        $customerCount = User::where('is_admin', false)->count();
        $activeProductCount = Product::where('status', true)->count();

        $chartData = $this->revenueChartData($start, $end, $paymentMethod);

        $recentOrders = Order::paymentConfirmed()
            ->when($paymentMethod, fn ($query) => $query->where('payment_method', $paymentMethod))
            ->latest()
            ->take(8)
            ->get();

        $lowStockThreshold = (int) Setting::get('low_stock_threshold', 5);
        $lowStockProducts = Product::where('stock', '<=', $lowStockThreshold)
            ->orderBy('stock')
            ->take(6)
            ->get();

        return view('admin.dashboard', [
            'revenueTotal' => $revenueTotal,
            'orderCount' => $orderCount,
            'customerCount' => $customerCount,
            'activeProductCount' => $activeProductCount,
            'chartData' => $chartData,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'periods' => self::PERIODS,
            'period' => $period,
            'periodLabel' => self::PERIODS[$period],
            'rangeLabel' => $start->format('d M Y').' – '.$end->format('d M Y'),
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolvePeriod(string $period, ?string $from, ?string $to): array
    {
        $now = now();

        return match ($period) {
            'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'last_3_months' => [$now->copy()->subMonths(3)->startOfDay(), $now->copy()->endOfDay()],
            'last_6_months' => [$now->copy()->subMonths(6)->startOfDay(), $now->copy()->endOfDay()],
            'this_fy' => $this->financialYearRange($now),
            'custom' => $this->customRange($from, $to, $now),
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function financialYearRange(Carbon $now): array
    {
        $fyStartYear = $now->month >= 4 ? $now->year : $now->year - 1;

        return [
            Carbon::create($fyStartYear, 4, 1)->startOfDay(),
            Carbon::create($fyStartYear + 1, 3, 31)->endOfDay(),
        ];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function customRange(?string $from, ?string $to, Carbon $now): array
    {
        try {
            $start = $from ? Carbon::parse($from)->startOfDay() : $now->copy()->startOfMonth();
        } catch (\Exception) {
            $start = $now->copy()->startOfMonth();
        }

        try {
            $end = $to ? Carbon::parse($to)->endOfDay() : $now->copy()->endOfDay();
        } catch (\Exception) {
            $end = $now->copy()->endOfDay();
        }

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    /**
     * @return array<int, array{label: string, total: float}>
     */
    private function revenueChartData(Carbon $start, Carbon $end, ?string $paymentMethod): array
    {
        $orders = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->paymentConfirmed()
            ->when($paymentMethod, fn ($query) => $query->where('payment_method', $paymentMethod))
            ->get(['created_at', 'grand_total']);

        $totalDays = (int) $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) + 1;

        if ($totalDays <= 31) {
            return $this->chartByDay($orders, $start, $end);
        }

        if ($totalDays <= 186) {
            return $this->chartByWeek($orders, $start, $end);
        }

        return $this->chartByMonth($orders, $start, $end);
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return array<int, array{label: string, total: float}>
     */
    private function chartByDay(Collection $orders, Carbon $start, Carbon $end): array
    {
        $byDay = $orders->groupBy(fn (Order $order) => $order->created_at->format('Y-m-d'))
            ->map(fn ($group) => (float) $group->sum('grand_total'));

        $days = [];
        $cursor = $start->copy()->startOfDay();
        $endDay = $end->copy()->startOfDay();

        while ($cursor->lte($endDay)) {
            $days[] = [
                'label' => $cursor->format('d M'),
                'total' => $byDay->get($cursor->format('Y-m-d'), 0.0),
            ];
            $cursor->addDay();
        }

        return $days;
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return array<int, array{label: string, total: float}>
     */
    private function chartByWeek(Collection $orders, Carbon $start, Carbon $end): array
    {
        $byWeek = $orders->groupBy(fn (Order $order) => $order->created_at->copy()->startOfWeek()->format('Y-m-d'))
            ->map(fn ($group) => (float) $group->sum('grand_total'));

        $weeks = [];
        $cursor = $start->copy()->startOfWeek();
        $endWeek = $end->copy()->startOfWeek();

        while ($cursor->lte($endWeek)) {
            $weeks[] = [
                'label' => $cursor->format('d M'),
                'total' => $byWeek->get($cursor->format('Y-m-d'), 0.0),
            ];
            $cursor->addWeek();
        }

        return $weeks;
    }

    /**
     * @param  Collection<int, Order>  $orders
     * @return array<int, array{label: string, total: float}>
     */
    private function chartByMonth(Collection $orders, Carbon $start, Carbon $end): array
    {
        $byMonth = $orders->groupBy(fn (Order $order) => $order->created_at->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('grand_total'));

        $months = [];
        $cursor = $start->copy()->startOfMonth();
        $endMonth = $end->copy()->startOfMonth();

        while ($cursor->lte($endMonth)) {
            $months[] = [
                'label' => $cursor->format('M Y'),
                'total' => $byMonth->get($cursor->format('Y-m'), 0.0),
            ];
            $cursor->addMonth();
        }

        return $months;
    }
}
