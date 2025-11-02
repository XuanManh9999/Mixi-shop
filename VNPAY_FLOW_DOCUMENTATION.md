# LUỒNG THANH TOÁN VNPAY - CHI TIẾT

## 📋 TỔNG QUAN

Hệ thống hỗ trợ 2 phương thức thanh toán:
1. **COD (Cash on Delivery)** - Thanh toán khi nhận hàng
2. **VNPay** - Thanh toán online qua cổng VNPay

---

## 🔄 LUỒNG THANH TOÁN VNPAY

### Bước 1: Tạo đơn hàng
**File:** `CheckoutController@place`

```php
// Người dùng điền thông tin và chọn phương thức thanh toán
Order::create([
    'payment_method' => 'vnpay',
    'payment_status' => 'unpaid',
    'status' => 'pending',
    // ... các thông tin khác
]);

// Redirect đến trang thank you
return redirect()->route('checkout.thankyou', ['order' => $order->id])
    ->with('order_created', true);
```

### Bước 2: Chuyển hướng đến VNPay
**File:** `PaymentController@vnpayPayment`

```php
// Tạo payment record
Payment::create([
    'order_id' => $order->id,
    'provider' => 'vnpay',
    'status' => 'pending',
    'amount' => $order->total_amount,
]);

// Tạo URL thanh toán VNPay
$paymentUrl = $vnpayService->createPaymentUrl($order);

// Chuyển hướng user đến VNPay
return redirect()->away($paymentUrl);
```

### Bước 3: User thanh toán trên VNPay
- User nhập thông tin thẻ/ngân hàng
- VNPay xử lý thanh toán
- VNPay gửi kết quả về hệ thống

### Bước 4: VNPay Return (User quay về)
**File:** `PaymentController@vnpayReturn`

```php
// Nhận thông tin từ VNPay
$vnpResponseCode = $inputData['vnp_ResponseCode'] ?? '';

if ($vnpResponseCode === '00') {
    // ✅ THANH TOÁN THÀNH CÔNG
    
    // 1. Cập nhật Payment
    $payment->update([
        'status' => 'paid',
        'paid_at' => now(),
        'vnp_TransactionNo' => $inputData['vnp_TransactionNo'],
        'vnp_BankCode' => $inputData['vnp_BankCode'],
        // ... các thông tin khác
    ]);
    
    // 2. Cập nhật Order
    $order->update([
        'payment_status' => 'paid',
        'status' => 'confirmed', // Đã xác nhận
    ]);
    
    // 3. Redirect với flag clear_cart
    return redirect()->route('checkout.thankyou', ['order' => $order->id])
        ->with('success', 'Thanh toán thành công!')
        ->with('clear_cart', true); // ⭐ FLAG ĐỂ XÓA GIỎ HÀNG
        
} else {
    // ❌ THANH TOÁN THẤT BẠI
    
    $payment->update(['status' => 'failed']);
    $order->update(['payment_status' => 'failed']);
    
    return redirect()->route('checkout.thankyou', ['order' => $order->id])
        ->with('error', 'Thanh toán thất bại!');
}
```

### Bước 5: Hiển thị trang Thank You & Clear Cart
**File:** `resources/views/storefront/thankyou.blade.php`

```javascript
// JavaScript tự động chạy khi load trang
const shouldClearCart = 
    session('clear_cart') ||        // VNPay thành công
    session('order_created') ||     // COD đã tạo đơn
    $order->payment_status === 'paid' || // Đã thanh toán
    $order->status === 'confirmed';      // Đã xác nhận

if (shouldClearCart) {
    localStorage.removeItem('mixishop_cart_v1'); // ⭐ XÓA GIỎ HÀNG
    
    // Cập nhật badge giỏ hàng
    badge.textContent = '0';
    badge.classList.add('d-none');
}
```

---

## 📊 CÁC TRẠNG THÁI ĐƠN HÀNG

### Payment Status (Trạng thái thanh toán)
- `unpaid` - Chưa thanh toán
- `paid` - Đã thanh toán ✅
- `failed` - Thanh toán thất bại
- `refunded` - Đã hoàn tiền

### Order Status (Trạng thái đơn hàng)
- `pending` - Chờ xử lý
- `confirmed` - Đã xác nhận ✅
- `preparing` - Đang chuẩn bị
- `shipping` - Đang giao hàng
- `delivered` - Đã giao hàng
- `cancelled` - Đã hủy
- `refunded` - Đã hoàn tiền

---

## ✅ ĐIỀU KIỆN XÓA GIỎ HÀNG

Giỏ hàng sẽ được xóa khi:

1. **VNPay thanh toán thành công**
   - `session('clear_cart') === true`
   - Được set trong `PaymentController@vnpayReturn`

2. **COD đã tạo đơn hàng**
   - `session('order_created') === true`
   - Được set trong `CheckoutController@place`

3. **Đơn hàng đã thanh toán**
   - `$order->payment_status === 'paid'`

4. **Đơn hàng đã xác nhận**
   - `$order->status === 'confirmed'`

---

## 🔐 BẢO MẬT

### VNPay Callback Verification
```php
public function verifyCallback($inputData)
{
    $vnpSecureHash = $inputData['vnp_SecureHash'];
    unset($inputData['vnp_SecureHash']);
    unset($inputData['vnp_SecureHashType']);
    
    // Sắp xếp và tạo hash
    ksort($inputData);
    $hashData = urlencode($key) . "=" . urlencode($value);
    
    $secureHash = hash_hmac('sha512', $hashData, $secretKey);
    
    return $secureHash === $vnpSecureHash; // ✅ Xác thực
}
```

---

## 📝 DATABASE SCHEMA

### Table: orders
- `payment_status` - unpaid/paid/failed
- `payment_method` - cod/vnpay
- `status` - pending/confirmed/...
- `total_amount` - Tổng tiền

### Table: payments
- `order_id` - FK to orders
- `provider` - vnpay/cod
- `status` - pending/paid/failed
- `vnp_TransactionNo` - Mã giao dịch VNPay
- `vnp_BankCode` - Mã ngân hàng
- `paid_at` - Thời gian thanh toán

---

## 🎯 ROUTES QUAN TRỌNG

```php
// Tạo đơn hàng
Route::post('/checkout/place', [CheckoutController::class, 'place'])
    ->name('checkout.place');

// Trang thank you
Route::get('/checkout/thankyou/{order}', [CheckoutController::class, 'thankyou'])
    ->name('checkout.thankyou');

// Thanh toán VNPay
Route::get('/payment/vnpay/{order}', [PaymentController::class, 'vnpayPayment'])
    ->name('payment.vnpay');

// VNPay return (user quay về)
Route::get('/payment/vnpay/return', [PaymentController::class, 'vnpayReturn'])
    ->name('payment.vnpay.return');

// VNPay callback (webhook)
Route::get('/payment/vnpay/callback', [PaymentController::class, 'vnpayCallback'])
    ->name('payment.vnpay.callback');
```

---

## 🐛 DEBUG & LOGGING

### Log quan trọng
```php
Log::info('VNPay Return received:', $inputData);
Log::info('VNPay payment successful', [
    'order_id' => $order->id,
    'transaction_no' => $vnpTransactionNo
]);
```

### Console log
```javascript
console.log('✅ Cart cleared successfully');
console.log('🎉 VNPay payment successful! Cart has been cleared.');
```

---

## ⚡ TEST FLOW

### Test COD
1. Thêm sản phẩm vào giỏ hàng
2. Checkout với COD
3. Kiểm tra: Giỏ hàng đã xóa ✅

### Test VNPay
1. Thêm sản phẩm vào giỏ hàng
2. Checkout với VNPay
3. Thanh toán trên VNPay (sandbox)
4. Quay về trang thank you
5. Kiểm tra: Giỏ hàng đã xóa ✅

---

## 🚨 LƯU Ý QUAN TRỌNG

1. **LocalStorage Key**: `mixishop_cart_v1`
2. **Session Keys**: 
   - `clear_cart` (VNPay success)
   - `order_created` (COD/VNPay order created)
3. **Cart Badge**: `#cartBadge`
4. **VNPay Response Code**:
   - `00` = Thành công
   - Khác = Thất bại

---

## ✅ CHECKLIST ĐẢM BẢO

- [x] Order được tạo trong database
- [x] Payment record được tạo
- [x] VNPay return được xử lý đúng
- [x] Order status được cập nhật (confirmed)
- [x] Payment status được cập nhật (paid)
- [x] Session flag được set (`clear_cart`)
- [x] JavaScript clear cart được chạy
- [x] Cart badge được cập nhật về 0
- [x] LocalStorage được xóa

---

Được tạo: {{ date('Y-m-d H:i:s') }}

