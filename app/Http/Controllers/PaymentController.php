<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $vnpayService;

    public function __construct(VNPayService $vnpayService)
    {
        $this->vnpayService = $vnpayService;
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    public function createVNPayPayment(Order $order)
    {
        try {
            // Kiểm tra order đã thanh toán chưa
            if ($order->payment_status === 'paid') {
                return redirect()->route('checkout.thankyou', ['order' => $order->id])
                               ->with('error', 'Đơn hàng đã được thanh toán!');
            }

            // Tạo URL thanh toán
            $paymentUrl = $this->vnpayService->createPaymentUrl($order, request()->ip());

            // Lưu payment URL vào session để có thể redirect sau khi xử lý
            session(['vnpay_payment_url' => $paymentUrl, 'vnpay_order_id' => $order->id]);

            return redirect($paymentUrl);

        } catch (\Exception $e) {
            Log::error('VNPay payment creation error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('checkout.thankyou', ['order' => $order->id])
                           ->with('error', 'Có lỗi xảy ra khi tạo thanh toán. Vui lòng thử lại!');
        }
    }

    /**
     * Callback từ VNPay
     */
    public function vnpayCallback(Request $request)
    {
        try {
            $inputData = $request->all();
            Log::info('VNPay Callback received:', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'all_params' => $inputData,
                'headers' => $request->headers->all()
            ]);

            // Nếu không có tham số VNPay, trả về JSON để debug
            if (empty($inputData) || !isset($inputData['vnp_TxnRef'])) {
                return response()->json([
                    'status' => 'debug',
                    'message' => 'VNPay callback endpoint is working',
                    'received_params' => $inputData,
                    'url' => $request->fullUrl(),
                    'method' => $request->method()
                ]);
            }

            // Xử lý callback
            $result = $this->vnpayService->handleCallback($inputData);

            if ($result['success']) {
                return redirect()->route('checkout.thankyou', ['order' => $result['order']->id])
                               ->with('success', $result['message']);
            } else {
                $redirectRoute = $result['order'] 
                    ? route('checkout.thankyou', ['order' => $result['order']->id])
                    : route('home');
                    
                return redirect($redirectRoute)
                               ->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('VNPay callback processing error', [
                'error' => $e->getMessage(),
                'input_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Trả về JSON để debug nếu có lỗi
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'received_params' => $request->all()
            ], 500);
        }
    }

    /**
     * Xử lý sau khi user quay về từ VNPay
     */
    public function vnpayReturn(Request $request)
    {
        try {
            $inputData = $request->all();
            Log::info('VNPay Return received:', $inputData);

            // Lấy order ID từ vnp_TxnRef
            $vnpTxnRef = $inputData['vnp_TxnRef'] ?? '';
            $orderId = explode('_', $vnpTxnRef)[0] ?? null;
            
            if (!$orderId) {
                return redirect()->route('home')->with('error', 'Thông tin thanh toán không hợp lệ!');
            }

            $order = Order::find($orderId);
            if (!$order) {
                return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng!');
            }

            // Tìm payment record
            $payment = Payment::where('order_id', $order->id)
                             ->where('provider', 'vnpay')
                             ->where('status', 'pending')
                             ->latest()
                             ->first();

            if (!$payment) {
                return redirect()->route('checkout.thankyou', ['order' => $order->id])
                               ->with('error', 'Không tìm thấy thông tin thanh toán!');
            }

            $vnpResponseCode = $inputData['vnp_ResponseCode'] ?? '';
            
            if ($vnpResponseCode === '00') {
                // THANH TOÁN THÀNH CÔNG
                
                // 1. Cập nhật payment
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'vnp_TransactionNo' => $inputData['vnp_TransactionNo'] ?? null,
                    'vnp_BankCode' => $inputData['vnp_BankCode'] ?? null,
                    'vnp_CardType' => $inputData['vnp_CardType'] ?? null,
                    'vnp_ResponseCode' => $vnpResponseCode,
                    'vnp_PayDate' => isset($inputData['vnp_PayDate']) ? 
                        \Carbon\Carbon::createFromFormat('YmdHis', $inputData['vnp_PayDate']) : null,
                    'raw_callback' => json_encode($inputData),
                ]);

                // 2. Cập nhật order
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed', // Đã xác nhận
                ]);

                Log::info('VNPay payment successful', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'transaction_no' => $inputData['vnp_TransactionNo'] ?? null
                ]);

                return redirect()->route('checkout.thankyou', ['order' => $order->id])
                               ->with('success', 'Thanh toán thành công!')
                               ->with('clear_cart', true); // Flag để clear cart
                
            } else {
                // THANH TOÁN THẤT BẠI
                
                $payment->update([
                    'status' => 'failed',
                    'vnp_ResponseCode' => $vnpResponseCode,
                    'raw_callback' => json_encode($inputData),
                ]);

                $order->update([
                    'payment_status' => 'failed',
                ]);

                $errorMessage = $this->vnpayService->getVNPayErrorMessage($vnpResponseCode);
                
                return redirect()->route('checkout.thankyou', ['order' => $order->id])
                               ->with('error', 'Thanh toán thất bại! ' . $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('VNPay return processing error', [
                'error' => $e->getMessage(),
                'input_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('home')
                           ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán!');
        }
    }


    /**
     * Thanh toán tiền mặt khi nhận hàng
     */
    public function cashOnDelivery(Order $order)
    {
        Payment::create([
            'order_id' => $order->id,
            'provider' => 'cash',
            'amount' => $order->total_amount,
            'currency' => 'VND',
            'status' => 'pending',
        ]);

        $order->update([
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
            'placed_at' => now(),
        ]);

        return redirect()->route('orders.success', $order)
                       ->with('success', 'Đặt hàng thành công! Bạn sẽ thanh toán khi nhận hàng.');
    }
}
