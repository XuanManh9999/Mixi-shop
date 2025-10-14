# Hướng Dẫn Hoàn Thiện Hệ Thống Thanh Toán VNPay - MixiShop

## Tổng Quan

Hệ thống thanh toán VNPay đã được tích hợp hoàn chỉnh vào MixiShop với các thông tin cấu hình:

-   **TMN_CODE**: 58X4B4HP
-   **SECRET_KEY**: VRLDWNVWDNPCOEPBZUTWSEDQAGXJCNGZ
-   **VERSION**: 2.1.0
-   **COMMAND**: pay
-   **ORDER_TYPE**: other

## Cấu Trúc Hệ Thống

### 1. Cấu Hình (config/services.php)

```php
'vnpay' => [
    'tmn_code' => env('VNPAY_TMN_CODE', '58X4B4HP'),
    'secret_key' => env('VNPAY_SECRET_KEY', 'VRLDWNVWDNPCOEPBZUTWSEDQAGXJCNGZ'),
    'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'return_url' => env('VNPAY_RETURN_URL', 'http://localhost:8000/payment/vnpay/callback'),
    'version' => '2.1.0',
    'command' => 'pay',
    'order_type' => 'other',
    'locale' => 'vn',
    'currency' => 'VND',
],
```

### 2. VNPayService (app/Services/VNPayService.php)

Service chính xử lý logic VNPay:

**Chức năng chính:**

-   `createPaymentUrl()`: Tạo URL thanh toán VNPay
-   `verifyCallback()`: Xác thực chữ ký callback
-   `handleCallback()`: Xử lý kết quả thanh toán
-   `parseVNPayDate()`: Parse định dạng ngày VNPay
-   `getVNPayErrorMessage()`: Lấy thông báo lỗi

**Tính năng:**

-   Tự động tạo mã giao dịch duy nhất
-   Xác thực chữ ký SHA512
-   Xử lý đầy đủ các mã lỗi VNPay
-   Logging chi tiết cho debug
-   Cập nhật trạng thái order và payment

### 3. PaymentController (app/Http/Controllers/PaymentController.php)

Controller xử lý requests thanh toán:

**Routes:**

-   `GET /payment/vnpay/{order}`: Tạo thanh toán VNPay
-   `GET /payment/vnpay/callback`: Xử lý callback từ VNPay

**Tính năng:**

-   Dependency injection VNPayService
-   Error handling toàn diện
-   Logging chi tiết
-   Redirect thông minh dựa trên kết quả

### 4. Models

#### Order Model

-   Thêm relationship với Payment
-   Các phương thức helper cho trạng thái
-   Format tiền tệ và labels

#### Payment Model

-   Lưu trữ thông tin VNPay callback
-   Các phương thức `markAsPaid()`, `markAsFailed()`
-   Scopes và accessors

### 5. Views

#### Checkout (resources/views/storefront/checkout.blade.php)

-   Form chọn phương thức thanh toán
-   Validation frontend
-   Tích hợp với cart system

#### Thank You (resources/views/storefront/thankyou.blade.php)

-   Hiển thị chi tiết đơn hàng
-   Thông tin thanh toán VNPay
-   Nút thanh toán lại (nếu thất bại)
-   Trạng thái thanh toán realtime

## Luồng Thanh Toán

### 1. Khách Hàng Đặt Hàng

```
Checkout Form → CheckoutController@place → Tạo Order → Redirect VNPay
```

### 2. Thanh Toán VNPay

```
VNPay URL → Khách hàng thanh toán → VNPay callback → Cập nhật trạng thái
```

### 3. Xử Lý Callback

```
Callback → Verify signature → Update Payment → Update Order → Redirect Thank You
```

## Cấu Hình Môi Trường

### File .env

```env
# VNPay Configuration
VNPAY_TMN_CODE=58X4B4HP
VNPAY_SECRET_KEY=VRLDWNVWDNPCOEPBZUTWSEDQAGXJCNGZ
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://localhost:8000/payment/vnpay/callback
```

### Production

Đối với production, thay đổi:

-   `VNPAY_URL` thành URL production của VNPay
-   `VNPAY_RETURN_URL` thành domain thực tế
-   Đảm bảo HTTPS cho callback URL

## Database Schema

### Bảng payments

```sql
- vnp_TransactionNo: Mã giao dịch VNPay
- vnp_BankCode: Mã ngân hàng
- vnp_CardType: Loại thẻ
- vnp_ResponseCode: Mã phản hồi
- vnp_PayDate: Ngày thanh toán
- vnp_SecureHash: Chữ ký bảo mật
- raw_callback: JSON callback đầy đủ
```

## Xử Lý Lỗi

### Các Mã Lỗi VNPay Được Hỗ Trợ

-   `00`: Thành công
-   `01`: Giao dịch chưa hoàn tất
-   `02`: Giao dịch bị lỗi
-   `11`: Sai mật khẩu 3D-Secure
-   `12`: Thẻ/Tài khoản bị khóa
-   `24`: Khách hàng hủy giao dịch
-   `51`: Không đủ số dư
-   `75`: Ngân hàng bảo trì
-   `99`: Lỗi khác

### Logging

-   Tất cả giao dịch được log chi tiết
-   Error tracking cho debug
-   Callback data được lưu trữ

## Tính Năng Nâng Cao

### 1. Thanh Toán Lại

-   Nút "Thanh toán lại" cho đơn hàng thất bại
-   Tự động tạo payment record mới
-   Giữ nguyên thông tin đơn hàng

### 2. Trạng Thái Realtime

-   Cập nhật trạng thái ngay lập tức
-   Thông báo success/error
-   UI responsive theo trạng thái

### 3. Security

-   Xác thực chữ ký SHA512
-   Validate tất cả tham số
-   Protection against replay attacks

## Testing

### Test Cases

1. **Thanh toán thành công**: Response code 00
2. **Thanh toán thất bại**: Các response code khác
3. **Chữ ký không hợp lệ**: Tampered callback
4. **Order không tồn tại**: Invalid order ID
5. **Payment đã xử lý**: Duplicate callback

### Test Data

```php
// Successful payment
$callbackData = [
    'vnp_ResponseCode' => '00',
    'vnp_TxnRef' => '1_1634567890',
    'vnp_TransactionNo' => '13456789',
    'vnp_BankCode' => 'NCB',
    // ... other parameters
];
```

## Monitoring & Maintenance

### 1. Logs Cần Theo Dõi

-   VNPay callback errors
-   Payment creation failures
-   Signature verification failures
-   Order status inconsistencies

### 2. Metrics

-   Payment success rate
-   Average payment time
-   Error distribution
-   Bank usage statistics

### 3. Alerts

-   High failure rate
-   Callback timeout
-   Signature verification failures

## Troubleshooting

### Lỗi Thường Gặp

1. **Chữ ký không hợp lệ**

    - Kiểm tra SECRET_KEY
    - Verify parameter sorting
    - Check URL encoding

2. **Callback không hoạt động**

    - Kiểm tra RETURN_URL
    - Verify route configuration
    - Check firewall/proxy

3. **Order không tìm thấy**
    - Verify TxnRef format
    - Check order ID parsing
    - Database connection issues

### Debug Commands

```bash
# Check logs
tail -f storage/logs/laravel.log | grep VNPay

# Test configuration
php artisan tinker
>>> config('services.vnpay')

# Clear cache
php artisan config:clear
php artisan cache:clear
```

## Kết Luận

Hệ thống thanh toán VNPay đã được tích hợp hoàn chỉnh với:

✅ **Cấu hình đầy đủ** với thông tin TMN_CODE và SECRET_KEY  
✅ **Service layer** chuyên nghiệp với VNPayService  
✅ **Controller** xử lý requests và callbacks  
✅ **Models** với relationships và business logic  
✅ **Views** hiển thị trạng thái thanh toán  
✅ **Error handling** toàn diện  
✅ **Logging** chi tiết cho monitoring  
✅ **Security** với signature verification  
✅ **User experience** với thanh toán lại

Hệ thống sẵn sàng cho production với đầy đủ tính năng và bảo mật cần thiết.
