<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Dashboard thống kê tổng quan
     */
    public function index(Request $request)
    {
        // Xử lý filter theo quý, tháng, tuần, ngày
        $period = $request->get('period', 'custom'); // day, week, month, quarter, custom
        $dateFrom = null;
        $dateTo = null;
        
        switch($period) {
            case 'day':
                $dateFrom = now()->toDateString();
                $dateTo = now()->toDateString();
                break;
            case 'week':
                $dateFrom = now()->startOfWeek()->toDateString();
                $dateTo = now()->endOfWeek()->toDateString();
                break;
            case 'month':
                $dateFrom = now()->startOfMonth()->toDateString();
                $dateTo = now()->endOfMonth()->toDateString();
                break;
            case 'quarter':
                $quarter = $request->get('quarter', ceil(now()->month / 3));
                $year = $request->get('year', now()->year);
                $monthStart = (($quarter - 1) * 3) + 1;
                $dateFrom = Carbon::create($year, $monthStart, 1)->startOfMonth()->toDateString();
                $dateTo = Carbon::create($year, $monthStart + 2, 1)->endOfMonth()->toDateString();
                break;
            default:
                // Custom date range
                $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
                $dateTo = $request->get('date_to', now()->toDateString());
        }

        // 1. TỔNG QUAN - Filter theo date range
        // CHỈ tính đơn hàng đã giao hàng (delivered) VÀ đã thanh toán (paid)
        $overview = [
            'total_users' => User::count(),
            'new_users_in_period' => User::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])->count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', 1)->count(),
            'total_orders_in_period' => Order::where('status', 'delivered') // Chỉ đơn hàng đã giao hàng
                ->where('payment_status', 'paid') // VÀ đã thanh toán
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->count(),
            'total_revenue_in_period' => Payment::where('status', 'paid')
                ->whereHas('order', function($q) {
                    $q->where('status', 'delivered'); // Chỉ doanh thu từ đơn hàng đã giao hàng
                })
                ->whereBetween('paid_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->sum('amount'),
        ];

        // 2. DOANH THU THEO NGÀY (30 ngày gần nhất hoặc filter)
        // CHỈ tính đơn hàng đã giao hàng (delivered) VÀ đã thanh toán (paid)
        $revenueByDate = Payment::select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'paid')
            ->whereHas('order', function($q) {
                $q->where('status', 'delivered'); // Chỉ doanh thu từ đơn hàng đã giao hàng
            })
            ->whereDate('paid_at', '>=', $dateFrom)
            ->whereDate('paid_at', '<=', $dateTo)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 3. ĐỚN HÀNG THEO TRẠNG THÁI - Filter theo date range
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->status => $item->count];
            });

        // 4. SẢN PHẨM BÁN CHẠY (Top 10) - Filter theo date range
        // CHỈ tính đơn hàng đã giao hàng (delivered) VÀ đã thanh toán (paid)
        $topProducts = OrderItem::select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->whereHas('order', function($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'delivered') // Chỉ đơn hàng đã giao hàng
                  ->where('payment_status', 'paid') // VÀ đã thanh toán
                  ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            })
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // 5. DANH MỤC BÁN CHẠY - Filter theo date range
        // CHỈ tính đơn hàng đã giao hàng (delivered) VÀ đã thanh toán (paid)
        $topCategories = OrderItem::select(
                'products.category_id',
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereHas('order', function($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'delivered') // Chỉ đơn hàng đã giao hàng
                  ->where('payment_status', 'paid') // VÀ đã thanh toán
                  ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            })
            ->groupBy('products.category_id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // 6. PHƯƠNG THỨC THANH TOÁN - Filter theo date range
        // Lấy từ bảng orders để phản ánh cả COD và các phương thức offline khác
        $paymentMethods = Order::select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('status', 'delivered') // Chỉ đơn hàng đã giao hàng
            ->where('payment_status', 'paid') // VÀ đã thanh toán
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                $item->provider = $item->payment_method ?? 'unknown';
                return $item;
            });

        // 7. KHÁCH HÀNG MUA NHIỀU NHẤT (Top 10) - Filter theo date range
        // CHỈ tính đơn hàng đã giao hàng (delivered) VÀ đã thanh toán (paid)
        $topCustomers = Order::select(
                'user_id',
                'users.name',
                'users.email',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_spent')
            )
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('status', 'delivered') // Chỉ đơn hàng đã giao hàng
            ->where('payment_status', 'paid') // VÀ đã thanh toán
            ->whereBetween('orders.created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy('user_id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        // 8. MÃ GIẢM GIÁ ĐƯỢC SỬ DỤNG NHIỀU NHẤT
        $topCoupons = Coupon::select('code', 'type', 'value', 'used_count')
            ->where('used_count', '>', 0)
            ->orderBy('used_count', 'desc')
            ->limit(10)
            ->get();

        // 9. ĐƠN HÀNG THEO GIỜ (24h hôm nay)
        $ordersByHour = Order::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->whereDate('created_at', today())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour');

        // Fill missing hours with 0
        $hourlyOrders = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyOrders[$i] = $ordersByHour->get($i, 0);
        }

        // 10. SO SÁNH THÁNG NÀY VS THÁNG TRƯỚC
        $currentMonth = [
            'revenue' => Payment::where('status', 'paid')
                               ->whereMonth('paid_at', now()->month)
                               ->whereYear('paid_at', now()->year)
                               ->sum('amount'),
            'orders' => Order::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count(),
        ];

        $lastMonth = [
            'revenue' => Payment::where('status', 'paid')
                               ->whereMonth('paid_at', now()->subMonth()->month)
                               ->whereYear('paid_at', now()->subMonth()->year)
                               ->sum('amount'),
            'orders' => Order::whereMonth('created_at', now()->subMonth()->month)
                            ->whereYear('created_at', now()->subMonth()->year)
                            ->count(),
        ];

        $comparison = [
            'revenue_change' => $lastMonth['revenue'] > 0 
                ? (($currentMonth['revenue'] - $lastMonth['revenue']) / $lastMonth['revenue']) * 100 
                : 100,
            'orders_change' => $lastMonth['orders'] > 0 
                ? (($currentMonth['orders'] - $lastMonth['orders']) / $lastMonth['orders']) * 100 
                : 100,
        ];

        return view('admin.statistics.index', compact(
            'overview',
            'revenueByDate',
            'ordersByStatus',
            'topProducts',
            'topCategories',
            'paymentMethods',
            'topCustomers',
            'topCoupons',
            'hourlyOrders',
            'currentMonth',
            'lastMonth',
            'comparison',
            'dateFrom',
            'dateTo',
            'period'
        ));
    }

    /**
     * Xem lịch sử đặt hàng của sản phẩm
     */
    public function productOrders($productId, Request $request)
    {
        $product = Product::findOrFail($productId);
        
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        // CHỈ tính đơn hàng đã giao hàng (delivered) VÀ đã thanh toán (paid)
        $orderItems = OrderItem::with(['order.user'])
            ->where('product_id', $productId)
            ->whereHas('order', function($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'delivered') // Chỉ đơn hàng đã giao hàng
                  ->where('payment_status', 'paid') // VÀ đã thanh toán
                  ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $stats = [
            'total_orders' => $orderItems->total(),
            'total_quantity' => OrderItem::where('product_id', $productId)
                ->whereHas('order', function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'delivered') // Chỉ đơn hàng đã giao hàng
                      ->where('payment_status', 'paid') // VÀ đã thanh toán
                      ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
                })
                ->sum('quantity'),
            'total_revenue' => OrderItem::where('product_id', $productId)
                ->whereHas('order', function($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'delivered') // Chỉ đơn hàng đã giao hàng
                      ->where('payment_status', 'paid') // VÀ đã thanh toán
                      ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
                })
                ->sum('total_price'),
        ];
        
        return view('admin.statistics.product-orders', compact(
            'product',
            'orderItems',
            'stats',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Thống kê doanh thu chi tiết
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year

        // Doanh thu theo khoảng thời gian
        $data = [];
        
        switch($period) {
            case 'day':
                // 30 ngày gần nhất
                for ($i = 29; $i >= 0; $i--) {
                    $date = now()->subDays($i)->toDateString();
                    $revenue = Payment::where('status', 'paid')
                                     ->whereDate('paid_at', $date)
                                     ->sum('amount');
                    $data[] = [
                        'label' => Carbon::parse($date)->format('d/m'),
                        'value' => $revenue
                    ];
                }
                break;
                
            case 'week':
                // 12 tuần gần nhất
                for ($i = 11; $i >= 0; $i--) {
                    $startOfWeek = now()->subWeeks($i)->startOfWeek();
                    $endOfWeek = now()->subWeeks($i)->endOfWeek();
                    $revenue = Payment::where('status', 'paid')
                                     ->whereBetween('paid_at', [$startOfWeek, $endOfWeek])
                                     ->sum('amount');
                    $data[] = [
                        'label' => 'T' . $startOfWeek->weekOfYear,
                        'value' => $revenue
                    ];
                }
                break;
                
            case 'month':
                // 12 tháng gần nhất
                for ($i = 11; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $revenue = Payment::where('status', 'paid')
                                     ->whereMonth('paid_at', $date->month)
                                     ->whereYear('paid_at', $date->year)
                                     ->sum('amount');
                    $data[] = [
                        'label' => $date->format('m/Y'),
                        'value' => $revenue
                    ];
                }
                break;
                
            case 'year':
                // 5 năm gần nhất
                for ($i = 4; $i >= 0; $i--) {
                    $year = now()->subYears($i)->year;
                    $revenue = Payment::where('status', 'paid')
                                     ->whereYear('paid_at', $year)
                                     ->sum('amount');
                    $data[] = [
                        'label' => $year,
                        'value' => $revenue
                    ];
                }
                break;
        }

        return view('admin.statistics.revenue', compact('data', 'period'));
    }

    /**
     * Thống kê sản phẩm
     */
    public function products(Request $request)
    {
        // Sản phẩm bán chạy
        // CHỈ tính đơn hàng đã giao hàng (delivered) VÀ đã thanh toán (paid)
        $bestSellers = OrderItem::select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->with('product')
            ->whereHas('order', function($q) {
                $q->where('status', 'delivered') // Chỉ đơn hàng đã giao hàng
                  ->where('payment_status', 'paid'); // VÀ đã thanh toán
            })
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_sold', 'desc')
            ->limit(20)
            ->get();

        // Sản phẩm tồn kho thấp
        $lowStock = Product::where('stock_qty', '<=', 10)
                          ->where('stock_qty', '>', 0)
                          ->orderBy('stock_qty')
                          ->limit(20)
                          ->get();

        // Sản phẩm hết hàng
        $outOfStock = Product::where('stock_qty', 0)
                            ->orderBy('updated_at', 'desc')
                            ->limit(20)
                            ->get();

        // Sản phẩm mới
        $newProducts = Product::orderBy('created_at', 'desc')
                             ->limit(10)
                             ->get();

        return view('admin.statistics.products', compact(
            'bestSellers',
            'lowStock',
            'outOfStock',
            'newProducts'
        ));
    }

    /**
     * Thống kê khách hàng
     */
    public function customers(Request $request)
    {
        // Top khách hàng
        // CHỈ tính đơn hàng đã giao hàng (delivered) VÀ đã thanh toán (paid)
        $topCustomers = Order::select(
                'user_id',
                'users.name',
                'users.email',
                'users.phone',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_spent'),
                DB::raw('AVG(total_amount) as avg_order_value')
            )
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('status', 'delivered') // Chỉ đơn hàng đã giao hàng
            ->where('payment_status', 'paid') // VÀ đã thanh toán
            ->groupBy('user_id', 'users.name', 'users.email', 'users.phone')
            ->orderBy('total_spent', 'desc')
            ->limit(20)
            ->get();

        // Khách hàng mới
        $newCustomers = User::where('is_admin', 0)
                           ->orderBy('created_at', 'desc')
                           ->limit(20)
                           ->get();

        // Thống kê user
        $userStats = [
            'total' => User::count(),
            'admins' => User::where('is_admin', 1)->count(),
            'customers' => User::where('is_admin', 0)->count(),
            'with_orders' => User::whereHas('orders')->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.statistics.customers', compact(
            'topCustomers',
            'newCustomers',
            'userStats'
        ));
    }

    /**
     * API endpoint cho chart data
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type', 'revenue');
        $period = $request->get('period', '30days');

        $data = [];

        switch($type) {
            case 'revenue':
                $data = $this->getRevenueChartData($period);
                break;
            case 'orders':
                $data = $this->getOrdersChartData($period);
                break;
            case 'products':
                $data = $this->getProductsChartData($period);
                break;
        }

        return response()->json($data);
    }

    /**
     * Get revenue chart data
     */
    private function getRevenueChartData($period)
    {
        $days = 30;
        if ($period === '7days') $days = 7;
        if ($period === '90days') $days = 90;

        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $revenue = Payment::where('status', 'paid')
                             ->whereDate('paid_at', $date)
                             ->sum('amount');
            $data[] = [
                'date' => Carbon::parse($date)->format('d/m'),
                'revenue' => (float) $revenue
            ];
        }

        return $data;
    }

    /**
     * Get orders chart data
     */
    private function getOrdersChartData($period)
    {
        $days = 30;
        if ($period === '7days') $days = 7;
        if ($period === '90days') $days = 90;

        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = Order::whereDate('created_at', $date)->count();
            $data[] = [
                'date' => Carbon::parse($date)->format('d/m'),
                'count' => $count
            ];
        }

        return $data;
    }

    /**
     * Get products chart data
     */
    private function getProductsChartData($period)
    {
        return Category::select(
                'categories.name',
                DB::raw('COUNT(DISTINCT products.id) as count')
            )
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->name,
                    'count' => $item->count
                ];
            });
    }
}
