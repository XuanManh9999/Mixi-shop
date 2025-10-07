<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['order.user']);

        // Tìm kiếm theo order ID hoặc transaction number
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('vnp_TransactionNo', 'like', "%{$search}%");
            });
        }

        // Filter theo provider
        if ($request->filled('provider')) {
            $query->where('provider', $request->get('provider'));
        }

        // Filter theo status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter theo ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 20);
        $payments = $query->paginate($perPage)->withQueryString();

        // Thống kê
        $stats = [
            'total' => Payment::count(),
            'paid' => Payment::where('status', 'paid')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'total_amount' => Payment::where('status', 'paid')->sum('amount'),
            'today_amount' => Payment::where('status', 'paid')
                                    ->whereDate('paid_at', today())
                                    ->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $payment->load(['order.user', 'order.orderItems.product']);
        
        // Parse raw callback nếu có
        $callbackData = null;
        if ($payment->raw_callback) {
            $callbackData = json_decode($payment->raw_callback, true);
        }

        return view('admin.payments.show', compact('payment', 'callbackData'));
    }

    /**
     * Mark payment as paid (manual)
     */
    public function markAsPaid(Payment $payment)
    {
        if ($payment->status === 'paid') {
            return back()->with('error', 'Thanh toán đã được xác nhận rồi!');
        }

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Cập nhật order
        $payment->order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        return back()->with('success', 'Đã xác nhận thanh toán thành công!');
    }

    /**
     * Mark payment as failed (manual)
     */
    public function markAsFailed(Request $request, Payment $payment)
    {
        if ($payment->status === 'paid') {
            return back()->with('error', 'Không thể hủy thanh toán đã được xác nhận!');
        }

        $payment->update([
            'status' => 'failed',
            'raw_callback' => json_encode([
                'reason' => $request->get('reason', 'Admin marked as failed'),
                'marked_by' => auth()->id(),
                'marked_at' => now(),
            ]),
        ]);

        $payment->order->update([
            'payment_status' => 'failed',
        ]);

        return back()->with('success', 'Đã đánh dấu thanh toán thất bại!');
    }

    /**
     * Export payments to CSV
     */
    public function export(Request $request)
    {
        $query = Payment::with(['order.user']);

        // Apply same filters as index
        if ($request->filled('provider')) {
            $query->where('provider', $request->get('provider'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'payments_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'ID',
                'Order ID',
                'Khách hàng',
                'Phương thức',
                'Số tiền',
                'Trạng thái',
                'Mã GD',
                'Ngân hàng',
                'Ngày tạo',
                'Ngày thanh toán'
            ]);

            // Data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->order_id,
                    $payment->order->user->name ?? 'N/A',
                    $payment->provider_label,
                    $payment->amount,
                    $payment->status_label,
                    $payment->vnp_TransactionNo ?? '',
                    $payment->vnp_BankCode ?? '',
                    $payment->created_at->format('d/m/Y H:i'),
                    $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Statistics page
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        // Tổng hợp theo provider
        $byProvider = Payment::select('provider', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
                            ->whereDate('created_at', '>=', $dateFrom)
                            ->whereDate('created_at', '<=', $dateTo)
                            ->where('status', 'paid')
                            ->groupBy('provider')
                            ->get();

        // Tổng hợp theo ngày
        $byDate = Payment::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
                        ->whereDate('created_at', '>=', $dateFrom)
                        ->whereDate('created_at', '<=', $dateTo)
                        ->where('status', 'paid')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();

        // Tổng hợp theo status
        $byStatus = Payment::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
                          ->whereDate('created_at', '>=', $dateFrom)
                          ->whereDate('created_at', '<=', $dateTo)
                          ->groupBy('status')
                          ->get();

        // Top payments
        $topPayments = Payment::with(['order.user'])
                             ->whereDate('created_at', '>=', $dateFrom)
                             ->whereDate('created_at', '<=', $dateTo)
                             ->where('status', 'paid')
                             ->orderBy('amount', 'desc')
                             ->limit(10)
                             ->get();

        return view('admin.payments.statistics', compact(
            'byProvider', 
            'byDate', 
            'byStatus', 
            'topPayments',
            'dateFrom',
            'dateTo'
        ));
    }
}
