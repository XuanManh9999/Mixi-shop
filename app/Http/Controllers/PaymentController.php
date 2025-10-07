<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Tạo URL thanh toán VNPay
     */
    public function createVNPayPayment(Order $order)
    {
        // Kiểm tra order đã thanh toán chưa
        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order)
                           ->with('error', 'Đơn hàng đã được thanh toán!');
        }

        // Tạo payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'vnpay',
            'amount' => $order->total_amount,
            'currency' => 'VND',
            'status' => 'pending',
        ]);

        // Cấu hình VNPay
        $vnp_TmnCode = env('VNPAY_TMN_CODE'); // Mã website
        $vnp_HashSecret = env('VNPAY_HASH_SECRET'); // Chuỗi bí mật
        $vnp_Url = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnp_Returnurl = route('payment.vnpay.callback');

        // Thông tin đơn hàng
        $vnp_TxnRef = $order->id; // Mã đơn hàng
        $vnp_OrderInfo = 'Thanh toán đơn hàng #' . $order->id;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order->total_amount * 100; // VNPay tính bằng đồng
        $vnp_Locale = 'vn';
        $vnp_BankCode = ''; // Để trống để hiển thị tất cả
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }

    /**
     * Callback từ VNPay
     */
    public function vnpayCallback(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $inputData = $request->all();
        
        Log::info('VNPay Callback:', $inputData);

        // Lấy vnp_SecureHash
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        // Sắp xếp dữ liệu
        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra checksum
        if ($secureHash != $vnp_SecureHash) {
            return redirect()->route('home')
                           ->with('error', 'Chữ ký không hợp lệ!');
        }

        // Lấy thông tin
        $vnp_TxnRef = $inputData['vnp_TxnRef'] ?? null;
        $vnp_ResponseCode = $inputData['vnp_ResponseCode'] ?? null;

        // Tìm order
        $order = Order::find($vnp_TxnRef);
        if (!$order) {
            return redirect()->route('home')
                           ->with('error', 'Không tìm thấy đơn hàng!');
        }

        // Tìm payment
        $payment = Payment::where('order_id', $order->id)
                         ->where('provider', 'vnpay')
                         ->where('status', 'pending')
                         ->latest()
                         ->first();

        if (!$payment) {
            return redirect()->route('home')
                           ->with('error', 'Không tìm thấy thông tin thanh toán!');
        }

        // Cập nhật payment
        $payment->update([
            'vnp_TransactionNo' => $inputData['vnp_TransactionNo'] ?? null,
            'vnp_BankCode' => $inputData['vnp_BankCode'] ?? null,
            'vnp_CardType' => $inputData['vnp_CardType'] ?? null,
            'vnp_ResponseCode' => $vnp_ResponseCode,
            'vnp_PayDate' => $inputData['vnp_PayDate'] ?? null,
            'vnp_SecureHash' => $vnp_SecureHash,
            'raw_callback' => json_encode($inputData),
        ]);

        // Kiểm tra kết quả thanh toán
        if ($vnp_ResponseCode == '00') {
            // Thanh toán thành công
            $payment->markAsPaid($inputData);

            return redirect()->route('orders.success', $order)
                           ->with('success', 'Thanh toán thành công!');
        } else {
            // Thanh toán thất bại
            $payment->markAsFailed('Response code: ' . $vnp_ResponseCode);

            return redirect()->route('orders.show', $order)
                           ->with('error', 'Thanh toán thất bại! Vui lòng thử lại.');
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
