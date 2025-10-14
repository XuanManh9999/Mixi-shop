<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class VNPayService
{
    private $tmnCode;
    private $secretKey;
    private $url;
    private $returnUrl;
    private $version;
    private $command;
    private $orderType;
    private $locale;
    private $currency;

    public function __construct()
    {
        $this->tmnCode = config('services.vnpay.tmn_code');
        $this->secretKey = config('services.vnpay.secret_key');
        $this->url = config('services.vnpay.url');
        $this->returnUrl = config('services.vnpay.return_url');
        $this->version = config('services.vnpay.version');
        $this->command = config('services.vnpay.command');
        $this->orderType = config('services.vnpay.order_type');
        $this->locale = config('services.vnpay.locale');
        $this->currency = config('services.vnpay.currency');
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    public function createPaymentUrl(Order $order, $ipAddress = null)
    {
        // Tạo payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'vnpay',
            'amount' => $order->total_amount,
            'currency' => $this->currency,
            'status' => 'pending',
        ]);

        $vnpTxnRef = $order->id . '_' . time(); // Mã giao dịch duy nhất
        $vnpOrderInfo = 'Thanh toan don hang #' . $order->id . ' - MixiShop';
        $vnpAmount = $order->total_amount * 100; // VNPay yêu cầu số tiền * 100
        $vnpIpAddr = $ipAddress ?: request()->ip();

        $inputData = array(
            "vnp_Version" => $this->version,
            "vnp_TmnCode" => $this->tmnCode,
            "vnp_Amount" => $vnpAmount,
            "vnp_Command" => $this->command,
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => $this->currency,
            "vnp_IpAddr" => $vnpIpAddr,
            "vnp_Locale" => $this->locale,
            "vnp_OrderInfo" => $vnpOrderInfo,
            "vnp_OrderType" => $this->orderType,
            "vnp_ReturnUrl" => $this->returnUrl,
            "vnp_TxnRef" => $vnpTxnRef,
        );

        // Sắp xếp tham số theo thứ tự alphabet
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

        $vnpUrl = $this->url . "?" . $query;
        
        // Tạo secure hash
        if (isset($this->secretKey)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->secretKey);
            $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Lưu thông tin giao dịch vào payment
        $payment->update([
            'vnp_TransactionNo' => $vnpTxnRef,
            'raw_callback' => json_encode($inputData),
        ]);

        return $vnpUrl;
    }

    /**
     * Xác thực callback từ VNPay
     */
    public function verifyCallback($inputData)
    {
        $vnpSecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        // Sắp xếp tham số
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

        $secureHash = hash_hmac('sha512', $hashData, $this->secretKey);

        return $secureHash === $vnpSecureHash;
    }

    /**
     * Xử lý callback từ VNPay
     */
    public function handleCallback($inputData)
    {
        try {
            // Xác thực chữ ký
            if (!$this->verifyCallback($inputData)) {
                Log::error('VNPay callback signature verification failed', $inputData);
                return [
                    'success' => false,
                    'message' => 'Chữ ký không hợp lệ',
                    'order' => null
                ];
            }

            $vnpTxnRef = $inputData['vnp_TxnRef'] ?? '';
            $vnpResponseCode = $inputData['vnp_ResponseCode'] ?? '';
            $vnpTransactionNo = $inputData['vnp_TransactionNo'] ?? '';
            $vnpBankCode = $inputData['vnp_BankCode'] ?? '';
            $vnpCardType = $inputData['vnp_CardType'] ?? '';
            $vnpPayDate = $inputData['vnp_PayDate'] ?? '';

            // Tìm order từ vnp_TxnRef
            $orderId = explode('_', $vnpTxnRef)[0];
            $order = Order::find($orderId);

            if (!$order) {
                Log::error('Order not found for VNPay callback', ['vnp_TxnRef' => $vnpTxnRef]);
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng',
                    'order' => null
                ];
            }

            // Tìm payment record
            $payment = Payment::where('order_id', $order->id)
                             ->where('provider', 'vnpay')
                             ->where('status', 'pending')
                             ->first();

            if (!$payment) {
                Log::error('Payment record not found for VNPay callback', [
                    'order_id' => $order->id,
                    'vnp_TxnRef' => $vnpTxnRef
                ]);
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin thanh toán',
                    'order' => $order
                ];
            }

            // Cập nhật thông tin payment
            $payment->update([
                'vnp_TransactionNo' => $vnpTransactionNo,
                'vnp_BankCode' => $vnpBankCode,
                'vnp_CardType' => $vnpCardType,
                'vnp_ResponseCode' => $vnpResponseCode,
                'vnp_PayDate' => $vnpPayDate ? $this->parseVNPayDate($vnpPayDate) : null,
                'raw_callback' => json_encode($inputData),
            ]);

            // Xử lý kết quả thanh toán
            if ($vnpResponseCode === '00') {
                // Thanh toán thành công
                $payment->markAsPaid($inputData);
                
                Log::info('VNPay payment successful', [
                    'order_id' => $order->id,
                    'vnp_TxnRef' => $vnpTxnRef,
                    'vnp_TransactionNo' => $vnpTransactionNo
                ]);

                return [
                    'success' => true,
                    'message' => 'Thanh toán thành công',
                    'order' => $order->fresh()
                ];
            } else {
                // Thanh toán thất bại
                $errorMessage = $this->getVNPayErrorMessage($vnpResponseCode);
                $payment->markAsFailed($errorMessage);

                Log::warning('VNPay payment failed', [
                    'order_id' => $order->id,
                    'vnp_TxnRef' => $vnpTxnRef,
                    'vnp_ResponseCode' => $vnpResponseCode,
                    'error_message' => $errorMessage
                ]);

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'order' => $order->fresh()
                ];
            }

        } catch (\Exception $e) {
            Log::error('VNPay callback processing error', [
                'error' => $e->getMessage(),
                'input_data' => $inputData
            ]);

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý thanh toán',
                'order' => null
            ];
        }
    }

    /**
     * Parse VNPay date format (YmdHis) to Carbon
     */
    private function parseVNPayDate($vnpPayDate)
    {
        try {
            return \Carbon\Carbon::createFromFormat('YmdHis', $vnpPayDate);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Lấy thông báo lỗi từ mã response VNPay
     */
    public function getVNPayErrorMessage($responseCode)
    {
        $messages = [
            '01' => 'Giao dịch chưa hoàn tất',
            '02' => 'Giao dịch bị lỗi',
            '04' => 'Giao dịch đảo (Khách hàng đã bị trừ tiền tại Ngân hàng nhưng GD chưa thành công ở VNPAY)',
            '05' => 'VNPAY đang xử lý giao dịch này (GD hoàn tiền)',
            '06' => 'VNPAY đã gửi yêu cầu hoàn tiền sang Ngân hàng (GD hoàn tiền)',
            '07' => 'Giao dịch bị nghi ngờ gian lận',
            '09' => 'GD Hoàn trả bị từ chối',
            '10' => 'Đã giao hàng',
            '11' => 'Giao dịch không thành công do: Khách hàng nhập sai mật khẩu xác thực 3D-Secure',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP)',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày',
            '75' => 'Ngân hàng thanh toán đang bảo trì',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)',
        ];

        return $messages[$responseCode] ?? 'Giao dịch không thành công';
    }
}
