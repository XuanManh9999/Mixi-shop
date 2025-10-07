# 💳 Hướng Dẫn Cấu Hình Thanh Toán

## 🎯 Tổng Quan

Hệ thống MixiShop đã tích hợp đầy đủ tính năng thanh toán với nhiều phương thức:

-   ✅ **VNPay** - Thanh toán qua cổng VNPay (thẻ ATM, Visa, MasterCard, QR Code)
-   ✅ **Tiền mặt** - Thanh toán khi nhận hàng (COD)
-   🔜 **MoMo** - Có thể thêm sau
-   🔜 **Chuyển khoản** - Có thể thêm sau

---

## 📊 Database Schema

### Bảng `payments`

```sql
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `provider` varchar(255) DEFAULT 'vnpay',
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(255) DEFAULT 'VND',
  `status` varchar(255) DEFAULT 'pending',
  `vnp_TransactionNo` varchar(255) DEFAULT NULL,
  `vnp_BankCode` varchar(255) DEFAULT NULL,
  `vnp_CardType` varchar(255) DEFAULT NULL,
  `vnp_ResponseCode` varchar(255) DEFAULT NULL,
  `vnp_PayDate` varchar(255) DEFAULT NULL,
  `vnp_SecureHash` varchar(255) DEFAULT NULL,
  `raw_callback` text,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_order_id_index` (`order_id`),
  KEY `payments_status_index` (`status`),
  KEY `payments_vnp_transactionno_index` (`vnp_TransactionNo`)
);
```

---

## ⚙️ Cấu Hình VNPay

### 1. Đăng Ký Tài Khoản VNPay

1. Truy cập: https://sandbox.vnpayment.vn/
2. Đăng ký tài khoản merchant (doanh nghiệp)
3. Lấy thông tin:
    - **TMN Code** (Mã website)
    - **Hash Secret** (Chuỗi bí mật)

### 2. Cấu Hình File `.env`

Thêm các dòng sau vào file `.env`:

```env
# VNPay Configuration
VNPAY_TMN_CODE=your_tmn_code_here
VNPAY_HASH_SECRET=your_hash_secret_here
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html

# Production URL (khi đưa lên production)
# VNPAY_URL=https://vnpayment.vn/paymentv2/vpcpay.html
```

### 3. Test Thông Tin (Sandbox)

**Thẻ ATM nội địa:**

```
Ngân hàng: NCB
Số thẻ: 9704198526191432198
Tên chủ thẻ: NGUYEN VAN A
Ngày phát hành: 07/15
Mật khẩu OTP: 123456
```

**Thẻ quốc tế (Visa/MasterCard):**

```
Số thẻ: 4111111111111111
Ngày hết hạn: 12/25
CVV: 123
```

---

## 🔌 API Endpoints

### 1. Tạo Thanh Toán VNPay

**POST** `/payment/vnpay/{order}`

**Yêu cầu:** User đã đăng nhập và là chủ đơn hàng

**Response:** Redirect đến cổng thanh toán VNPay

### 2. Callback từ VNPay

**GET** `/payment/vnpay/callback`

**Parameters:**

-   `vnp_TxnRef`: Mã đơn hàng
-   `vnp_ResponseCode`: Mã kết quả
-   `vnp_SecureHash`: Chữ ký bảo mật
-   ... (nhiều params khác)

**Response:** Redirect về trang kết quả

### 3. Thanh Toán COD

**POST** `/payment/cash/{order}`

**Yêu cầu:** User đã đăng nhập và là chủ đơn hàng

**Response:** Redirect về trang thành công

---

## 💻 Sử Dụng Trong Code

### 1. Model Payment

```php
use App\Models\Payment;

// Tạo payment
$payment = Payment::create([
    'order_id' => $order->id,
    'provider' => 'vnpay',
    'amount' => 500000,
    'currency' => 'VND',
    'status' => 'pending',
]);

// Lấy payment của order
$payments = $order->payments;
$latestPayment = $order->latestPayment;

// Kiểm tra trạng thái
if ($payment->isPaid()) {
    echo 'Đã thanh toán';
}

// Mark as paid
$payment->markAsPaid($vnpayData);

// Mark as failed
$payment->markAsFailed('Lỗi kết nối');
```

### 2. Controller Payment

```php
use App\Http\Controllers\PaymentController;

// Trong OrderController hoặc CheckoutController
public function checkout(Request $request)
{
    // ... xử lý tạo order ...

    // Redirect đến thanh toán
    if ($request->payment_method === 'vnpay') {
        return redirect()->route('payment.vnpay', $order);
    } else {
        return redirect()->route('payment.cash', $order);
    }
}
```

### 3. Blade Template

```blade
<!-- Form chọn phương thức thanh toán -->
<form method="POST" action="{{ route('checkout.process') }}">
    @csrf

    <div class="payment-methods">
        <label>
            <input type="radio" name="payment_method" value="vnpay" checked>
            <span>Thanh toán VNPay</span>
        </label>

        <label>
            <input type="radio" name="payment_method" value="cash">
            <span>Thanh toán khi nhận hàng (COD)</span>
        </label>
    </div>

    <button type="submit">Đặt hàng</button>
</form>

<!-- Hiển thị trạng thái thanh toán -->
<div class="payment-status">
    @if($order->latestPayment)
        <span class="badge bg-{{ $order->latestPayment->status_color }}">
            {{ $order->latestPayment->status_label }}
        </span>

        @if($order->latestPayment->isPaid())
            <p>Đã thanh toán: {{ $order->latestPayment->formatted_amount }}</p>
            <p>Ngân hàng: {{ $order->latestPayment->vnp_BankCode }}</p>
            <p>Mã GD: {{ $order->latestPayment->vnp_TransactionNo }}</p>
        @endif
    @endif
</div>
```

---

## 🔄 Luồng Thanh Toán

### VNPay Flow

```
1. User chọn sản phẩm → Giỏ hàng
                ↓
2. Checkout → Tạo Order (status: pending)
                ↓
3. Chọn "Thanh toán VNPay"
                ↓
4. System tạo Payment record (status: pending)
                ↓
5. System tạo URL VNPay → Redirect user
                ↓
6. User nhập thông tin thẻ trên VNPay
                ↓
7. VNPay xử lý → Callback về /payment/vnpay/callback
                ↓
8. System verify signature + cập nhật Payment
                ↓
9. Nếu thành công:
   - Payment: status = 'paid', paid_at = now()
   - Order: payment_status = 'paid', status = 'processing'
                ↓
10. Redirect user → Trang thành công
```

### COD Flow

```
1. User chọn sản phẩm → Giỏ hàng
                ↓
2. Checkout → Tạo Order
                ↓
3. Chọn "Thanh toán khi nhận hàng"
                ↓
4. System tạo Payment record (status: pending, provider: cash)
                ↓
5. Order: payment_status = 'pending', status = 'pending'
                ↓
6. Redirect → Trang thành công
                ↓
7. Admin xác nhận đơn → Giao hàng
                ↓
8. Shipper thu tiền → Admin cập nhật Payment: status = 'paid'
```

---

## 🎨 Response Codes VNPay

| Code | Ý nghĩa                                                                                          |
| ---- | ------------------------------------------------------------------------------------------------ |
| 00   | Giao dịch thành công                                                                             |
| 07   | Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường)         |
| 09   | Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking |
| 10   | Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần  |
| 11   | Giao dịch không thành công do: Đã hết hạn chờ thanh toán                                         |
| 12   | Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa                              |
| 13   | Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP)               |
| 24   | Giao dịch không thành công do: Khách hàng hủy giao dịch                                          |
| 51   | Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư                            |
| 65   | Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày  |
| 75   | Ngân hàng thanh toán đang bảo trì                                                                |
| 79   | Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định               |
| 99   | Các lỗi khác                                                                                     |

---

## 🔒 Bảo Mật

### 1. Xác Thực Chữ Ký (Signature)

VNPay sử dụng HMAC-SHA512 để ký dữ liệu:

```php
$secureHash = hash_hmac('sha512', $data, $hashSecret);
```

**QUAN TRỌNG:**

-   ✅ LUÔN verify signature từ VNPay callback
-   ✅ KHÔNG bao giờ tin tưởng dữ liệu từ client
-   ✅ Lưu `raw_callback` để audit

### 2. HTTPS Required

-   Production PHẢI sử dụng HTTPS
-   VNPay sẽ reject callback URL không phải HTTPS

### 3. IP Whitelist

Có thể config VNPay chỉ callback về IP cố định

---

## 🐛 Troubleshooting

### Lỗi: "Chữ ký không hợp lệ"

**Nguyên nhân:**

-   Hash Secret sai
-   Dữ liệu không sắp xếp đúng thứ tự
-   Có params bị modify

**Giải pháp:**

```php
// Log để debug
Log::info('VNPay Callback:', $request->all());
Log::info('Calculated Hash:', $secureHash);
Log::info('Received Hash:', $vnp_SecureHash);
```

### Lỗi: "Không tìm thấy đơn hàng"

**Nguyên nhân:**

-   `vnp_TxnRef` không khớp với Order ID

**Giải pháp:**

-   Check database có order với ID đó không
-   Đảm bảo `vnp_TxnRef = $order->id`

### Payment status không cập nhật

**Nguyên nhân:**

-   Callback URL không đúng
-   Server không public (localhost)
-   Firewall block

**Giải pháp:**

-   Test trên server public hoặc dùng ngrok
-   Check VNPay logs

---

## 📝 Checklist Go Live

-   [ ] Đã test thanh toán sandbox thành công
-   [ ] Đã có tài khoản VNPay production
-   [ ] Đã cập nhật `VNPAY_TMN_CODE` production
-   [ ] Đã cập nhật `VNPAY_HASH_SECRET` production
-   [ ] Đã cập nhật `VNPAY_URL` production
-   [ ] Website đã có SSL (HTTPS)
-   [ ] Callback URL đã đăng ký với VNPay
-   [ ] Đã test thanh toán production với số tiền nhỏ
-   [ ] Đã config email thông báo khi có payment
-   [ ] Đã có quy trình xử lý refund

---

## 🎉 Hoàn Tất!

Hệ thống thanh toán đã sẵn sàng! Bạn có thể:

✅ Nhận thanh toán VNPay (ATM, Visa, QR)  
✅ Nhận thanh toán COD  
✅ Track tất cả giao dịch  
✅ Xử lý callback tự động  
✅ Bảo mật với signature verification

**Happy Coding!** 🚀
