<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng của user
     */
    public function index(Request $request)
    {
        $query = Order::with(['orderItems.product', 'latestPayment'])
                     ->where('user_id', auth()->id() ?? 0);

        // Filter theo status nếu có
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter theo payment status nếu có
        if ($request->has('payment_status') && $request->payment_status !== '') {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('storefront.orders.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== (auth()->id() ?? 0)) {
            abort(403, 'Bạn không có quyền xem đơn hàng này');
        }

        $order->load(['orderItems.product', 'payments']);

        // Kiểm tra và cập nhật trạng thái đơn hàng nếu cần
        $this->checkAndUpdateOrderStatus($order);

        return view('storefront.orders.show', compact('order'));
    }

    /**
     * Theo dõi đơn hàng bằng ID (không cần đăng nhập)
     */
    public function track(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'order_id' => 'required|integer',
                'phone' => 'required|string|min:10|max:15'
            ]);

            $order = Order::with(['orderItems.product', 'latestPayment'])
                         ->where('id', $request->order_id)
                         ->where('ship_phone', $request->phone)
                         ->first();

            if (!$order) {
                return back()->with('error', 'Không tìm thấy đơn hàng với thông tin đã nhập!');
            }

            // Kiểm tra và cập nhật trạng thái
            $this->checkAndUpdateOrderStatus($order);

            return view('storefront.orders.track-result', compact('order'));
        }

        return view('storefront.orders.track');
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel(Order $order)
    {
        // Kiểm tra quyền
        if ($order->user_id !== (auth()->id() ?? 0)) {
            abort(403, 'Bạn không có quyền hủy đơn hàng này');
        }

        // Chỉ cho phép hủy đơn hàng pending hoặc confirmed
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại!');
        }

        DB::transaction(function () use ($order) {
            // Cập nhật trạng thái đơn hàng
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'cancelled'
            ]);

            // Hủy payment nếu có
            $pendingPayment = $order->payments()->where('status', 'pending')->first();
            if ($pendingPayment) {
                $pendingPayment->update(['status' => 'cancelled']);
            }
        });

        return back()->with('success', 'Đơn hàng đã được hủy thành công!');
    }

    /**
     * Kiểm tra và cập nhật trạng thái đơn hàng
     */
    private function checkAndUpdateOrderStatus(Order $order)
    {
        // Chỉ xử lý đơn hàng VNPay chưa thanh toán
        if ($order->payment_method === 'vnpay' && $order->payment_status === 'unpaid') {
            $createdAt = $order->created_at;
            $now = Carbon::now();
            
            // Kiểm tra đã quá 15 phút chưa
            if ($now->diffInMinutes($createdAt) >= 15) {
                DB::transaction(function () use ($order) {
                    // Hủy đơn hàng
                    $order->update([
                        'status' => 'cancelled',
                        'payment_status' => 'expired'
                    ]);

                    // Hủy payment
                    $pendingPayment = $order->payments()
                                          ->where('provider', 'vnpay')
                                          ->where('status', 'pending')
                                          ->first();
                    
                    if ($pendingPayment) {
                        $pendingPayment->update([
                            'status' => 'expired',
                            'raw_callback' => json_encode(['reason' => 'Payment timeout after 15 minutes'])
                        ]);
                    }
                });

                \Log::info('Order auto-cancelled due to payment timeout', [
                    'order_id' => $order->id,
                    'created_at' => $createdAt,
                    'cancelled_at' => $now
                ]);
            }
        }
    }

    /**
     * Lấy timeline trạng thái đơn hàng
     */
    public function getOrderTimeline(Order $order)
    {
        $timeline = [];

        // Đơn hàng được tạo
        $timeline[] = [
            'status' => 'created',
            'title' => 'Đơn hàng được tạo',
            'description' => 'Đơn hàng #' . $order->id . ' đã được tạo thành công',
            'time' => $order->created_at,
            'icon' => 'fas fa-plus-circle',
            'color' => 'primary'
        ];

        // Thanh toán
        if ($order->payment_status === 'paid') {
            $payment = $order->latestPayment;
            $timeline[] = [
                'status' => 'paid',
                'title' => 'Thanh toán thành công',
                'description' => 'Đã thanh toán ' . $order->formatted_total . ' qua ' . $order->payment_method_label,
                'time' => $payment ? $payment->paid_at : $order->updated_at,
                'icon' => 'fas fa-credit-card',
                'color' => 'success'
            ];
        } elseif ($order->payment_status === 'expired') {
            $timeline[] = [
                'status' => 'expired',
                'title' => 'Thanh toán hết hạn',
                'description' => 'Đơn hàng đã bị hủy do không thanh toán trong 15 phút',
                'time' => $order->updated_at,
                'icon' => 'fas fa-clock',
                'color' => 'danger'
            ];
        } elseif ($order->payment_method === 'cod') {
            $timeline[] = [
                'status' => 'cod',
                'title' => 'Thanh toán khi nhận hàng',
                'description' => 'Bạn sẽ thanh toán khi nhận được hàng',
                'time' => $order->created_at,
                'icon' => 'fas fa-hand-holding-usd',
                'color' => 'warning'
            ];
        }

        // Trạng thái đơn hàng
        $statusMap = [
            'confirmed' => [
                'title' => 'Đơn hàng đã xác nhận',
                'description' => 'Đơn hàng đã được xác nhận và đang chuẩn bị',
                'icon' => 'fas fa-check-circle',
                'color' => 'info'
            ],
            'preparing' => [
                'title' => 'Đang chuẩn bị hàng',
                'description' => 'Đơn hàng đang được chuẩn bị để giao',
                'icon' => 'fas fa-box',
                'color' => 'primary'
            ],
            'shipping' => [
                'title' => 'Đang giao hàng',
                'description' => 'Đơn hàng đang trên đường giao đến bạn',
                'icon' => 'fas fa-shipping-fast',
                'color' => 'info'
            ],
            'delivered' => [
                'title' => 'Đã giao hàng',
                'description' => 'Đơn hàng đã được giao thành công',
                'icon' => 'fas fa-check-double',
                'color' => 'success'
            ],
            'cancelled' => [
                'title' => 'Đơn hàng đã hủy',
                'description' => 'Đơn hàng đã được hủy',
                'icon' => 'fas fa-times-circle',
                'color' => 'danger'
            ]
        ];

        if (isset($statusMap[$order->status])) {
            $statusInfo = $statusMap[$order->status];
            $timeline[] = [
                'status' => $order->status,
                'title' => $statusInfo['title'],
                'description' => $statusInfo['description'],
                'time' => $order->updated_at,
                'icon' => $statusInfo['icon'],
                'color' => $statusInfo['color']
            ];
        }

        // Sắp xếp theo thời gian
        usort($timeline, function ($a, $b) {
            return $a['time']->timestamp - $b['time']->timestamp;
        });

        return $timeline;
    }
}
