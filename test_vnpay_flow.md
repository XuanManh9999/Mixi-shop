# HƯỚNG DẪN TEST LUỒNG THANH TOÁN VNPAY

## 🎯 MỤC TIÊU

Test đảm bảo sau khi thanh toán VNPay thành công:
1. ✅ Giỏ hàng được xóa
2. ✅ Đơn hàng được cập nhật đúng trạng thái
3. ✅ User nhìn thấy thông báo thành công

---

## 📝 CHUẨN BỊ

### 1. Kiểm tra cấu hình VNPay
File: `.env`
```env
VNPAY_TMN_CODE=your_tmn_code
VNPAY_HASH_SECRET=your_hash_secret
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://your-domain/payment/vnpay/return
VNPAY_CALLBACK_URL=http://your-domain/payment/vnpay/callback
```

### 2. Clear cache
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

---

## 🧪 TEST CASES

### Test Case 1: COD (Thanh toán khi nhận hàng)

**Bước thực hiện:**
1. Thêm 2-3 sản phẩm vào giỏ hàng
2. Vào trang checkout
3. Điền thông tin giao hàng
4. Chọn phương thức: **COD**
5. Click "Đặt hàng"

**Kết quả mong đợi:**
- ✅ Redirect đến trang Thank You
- ✅ Giỏ hàng được xóa (badge = 0)
- ✅ Hiển thị thông báo "Lên đơn hàng thành công"
- ✅ Order status = `pending`
- ✅ Payment status = `unpaid`

**Kiểm tra:**
```javascript
// Mở Console (F12)
// Kiểm tra log:
// "✅ Cart cleared successfully"
// "🎉 Order created! Cart has been cleared."

// Kiểm tra localStorage
localStorage.getItem('mixishop_cart_v1') // null
```

---

### Test Case 2: VNPay - Thanh toán thành công

**Bước thực hiện:**
1. Thêm 2-3 sản phẩm vào giỏ hàng
2. Vào trang checkout
3. Điền thông tin giao hàng
4. Chọn phương thức: **VNPay**
5. Click "Đặt hàng"
6. Trên trang Thank You, click "Thanh toán ngay"
7. Trên VNPay sandbox:
   - Chọn ngân hàng: **NCB**
   - Số thẻ: `9704198526191432198`
   - Tên: `NGUYEN VAN A`
   - Ngày phát hành: `07/15`
   - OTP: `123456`
8. Click "Thanh toán"

**Kết quả mong đợi:**
- ✅ VNPay xử lý thành công
- ✅ Redirect về trang Thank You
- ✅ Hiển thị "Thanh toán thành công!"
- ✅ Giỏ hàng được xóa (badge = 0)
- ✅ Order status = `confirmed`
- ✅ Payment status = `paid`
- ✅ Hiển thị thông tin giao dịch VNPay

**Kiểm tra Console:**
```javascript
// "✅ Cart cleared successfully"
// "🎉 VNPay payment successful! Cart has been cleared."

// Kiểm tra localStorage
localStorage.getItem('mixishop_cart_v1') // null
```

**Kiểm tra Database:**
```sql
-- Kiểm tra order
SELECT id, status, payment_status, payment_method, total_amount 
FROM orders 
WHERE id = [order_id];
-- status = 'confirmed'
-- payment_status = 'paid'

-- Kiểm tra payment
SELECT id, order_id, status, vnp_TransactionNo, paid_at
FROM payments
WHERE order_id = [order_id];
-- status = 'paid'
-- vnp_TransactionNo có giá trị
-- paid_at có giá trị
```

---

### Test Case 3: VNPay - Thanh toán thất bại

**Bước thực hiện:**
1. Thêm sản phẩm vào giỏ hàng
2. Checkout với VNPay
3. Trên VNPay sandbox, click **"Hủy giao dịch"**

**Kết quả mong đợi:**
- ✅ Redirect về trang Thank You
- ✅ Hiển thị "Thanh toán thất bại!"
- ✅ Giỏ hàng KHÔNG bị xóa (vẫn có sản phẩm)
- ✅ Order status = `pending`
- ✅ Payment status = `failed`

**Kiểm tra:**
```javascript
// Giỏ hàng VẪN CÒN
localStorage.getItem('mixishop_cart_v1') // null or có data
```

---

### Test Case 4: Thanh toán lại sau khi thất bại

**Bước thực hiện:**
1. Sau Test Case 3 (thất bại)
2. Trên trang Thank You, click "Thanh toán lại"
3. Thanh toán thành công

**Kết quả mong đợi:**
- ✅ Thanh toán thành công
- ✅ Giỏ hàng được xóa
- ✅ Order status = `confirmed`
- ✅ Payment status = `paid`

---

## 🔍 DEBUGGING

### Kiểm tra Session
```php
// Trong PaymentController@vnpayReturn
dd(session()->all());
// Phải có: 'clear_cart' => true
```

### Kiểm tra JavaScript
```javascript
// Trong thankyou.blade.php
console.log('Session clear_cart:', {{ session('clear_cart') ? 'true' : 'false' }});
console.log('Order payment_status:', '{{ $order->payment_status }}');
console.log('Order status:', '{{ $order->status }}');
```

### Kiểm tra Log
```bash
tail -f storage/logs/laravel.log | grep VNPay
```

---

## ✅ CHECKLIST CUỐI CÙNG

### Code đã được cập nhật:
- [x] `PaymentController@vnpayReturn` - Set session `clear_cart`
- [x] `CheckoutController@place` - Set session `order_created`
- [x] `thankyou.blade.php` - JavaScript clear cart

### Database:
- [x] Order status được cập nhật
- [x] Payment status được cập nhật
- [x] Payment record có đầy đủ thông tin VNPay

### Frontend:
- [x] Cart badge hiển thị đúng
- [x] LocalStorage được clear
- [x] Thông báo thành công hiển thị

### Test:
- [ ] Test COD - Pass
- [ ] Test VNPay Success - Pass
- [ ] Test VNPay Failed - Pass
- [ ] Test Thanh toán lại - Pass

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề:
1. Kiểm tra Laravel log: `storage/logs/laravel.log`
2. Kiểm tra Console log (F12)
3. Kiểm tra Network tab xem request/response
4. Kiểm tra database xem data đã lưu chưa

---

Được tạo: {{ date('Y-m-d H:i:s') }}

