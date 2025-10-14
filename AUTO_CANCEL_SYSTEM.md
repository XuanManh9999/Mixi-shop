# Hệ thống tự động hủy đơn hàng - MixiShop

## 📋 Tổng quan

Hệ thống tự động hủy đơn hàng được thiết kế để quản lý vòng đời đơn hàng một cách hiệu quả, đảm bảo không có đơn hàng nào bị "treo" quá lâu.

## ⏰ Các trường hợp tự động hủy

### 1. Đơn hàng Pending (Chờ xác nhận)

-   **Thời gian**: 15 phút từ lúc tạo đơn (`created_at`)
-   **Điều kiện**: `status = 'pending'`
-   **Lý do hủy**: "Tự động hủy: Quá 15 phút chưa xác nhận"

### 2. Đơn hàng Confirmed chưa thanh toán

-   **Thời gian**: 15 phút từ lúc xác nhận (`confirmed_at`)
-   **Điều kiện**: `status = 'confirmed'` AND `payment_status = 'unpaid'`
-   **Lý do hủy**: "Tự động hủy: Quá 15 phút chưa thanh toán sau khi xác nhận"

### 3. Đơn hàng VNPay (Giữ nguyên)

-   **Thời gian**: 15 phút từ lúc tạo đơn (`created_at`)
-   **Điều kiện**: `payment_method = 'vnpay'` AND `payment_status = 'unpaid'`
-   **Lý do hủy**: Xử lý riêng trong logic VNPay

## 🗃️ Cấu trúc Database

### Trường mới trong bảng `orders`

```sql
ALTER TABLE orders ADD COLUMN confirmed_at TIMESTAMP NULL AFTER placed_at;
```

### Các trường liên quan

-   `created_at`: Thời điểm tạo đơn hàng
-   `confirmed_at`: Thời điểm admin xác nhận đơn hàng
-   `status`: Trạng thái đơn hàng (pending, confirmed, preparing, shipping, delivered, cancelled)
-   `payment_status`: Trạng thái thanh toán (unpaid, paid, failed, refunded)

## 🔧 Các phương thức trong Order Model

### Kiểm tra hết hạn

```php
// Kiểm tra pending hết hạn
$order->isPendingExpired()

// Kiểm tra confirmed hết hạn
$order->isConfirmedExpired()

// Kiểm tra có cần tự động hủy không
$order->shouldAutoCancel()
```

### Lấy thời gian còn lại

```php
// Thời gian còn lại để xác nhận (giây)
$order->pending_time_left

// Thời gian còn lại để thanh toán sau confirmed (giây)
$order->confirmed_time_left
```

### Thao tác đơn hàng

```php
// Tự động hủy đơn hàng
$order->autoCancel($reason)

// Xác nhận đơn hàng (set confirmed_at)
$order->confirmOrder()
```

## 🤖 Artisan Command

### Chạy tự động hủy

```bash
# Dry run - chỉ xem không thực hiện
php artisan orders:auto-cancel --dry-run

# Thực hiện hủy đơn hàng
php artisan orders:auto-cancel
```

### Kết quả mẫu

```
🔍 Đang kiểm tra các đơn hàng hết hạn...
⚠️  Tìm thấy 13 đơn hàng hết hạn:
   - 13 đơn pending chưa xác nhận
   - 0 đơn confirmed chưa thanh toán

✅ Đã hủy đơn #25 (pending hết hạn)
✅ Đã hủy đơn #21 (pending hết hạn)

🎉 Hoàn thành! Đã hủy 13/13 đơn hàng.
```

## 🕐 Cron Job (Khuyến nghị)

### Thiết lập cron để chạy mỗi 5 phút

```bash
# Thêm vào crontab
*/5 * * * * cd /path/to/project && php artisan orders:auto-cancel >> /var/log/auto-cancel.log 2>&1
```

### Hoặc sử dụng Laravel Scheduler

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('orders:auto-cancel')
             ->everyFiveMinutes()
             ->withoutOverlapping();
}
```

## 🎨 Giao diện người dùng

### Countdown Timer

-   **Pending**: Hiển thị thời gian còn lại để được xác nhận
-   **Confirmed**: Hiển thị thời gian còn lại để thanh toán
-   **VNPay**: Hiển thị thời gian còn lại để thanh toán VNPay

### Thông báo hết hạn

-   Tự động reload trang khi countdown về 0
-   Hiển thị thông báo "Đã hết hạn" với màu đỏ
-   Ẩn các nút action không còn phù hợp

## 🧪 Testing

### Test API

```bash
# Kiểm tra đơn hàng hết hạn
curl http://localhost:8000/test-auto-cancel
```

### Test Command

```bash
# Dry run
php artisan orders:auto-cancel --dry-run

# Thực hiện
php artisan orders:auto-cancel
```

## 📊 Monitoring & Logging

### Log Files

-   Command execution: `storage/logs/laravel.log`
-   Auto cancel events: Tìm kiếm "Auto cancelled"

### Metrics cần theo dõi

-   Số đơn hàng bị hủy tự động mỗi ngày
-   Tỷ lệ đơn hàng pending vs confirmed bị hủy
-   Thời gian trung bình từ tạo đến xác nhận đơn hàng

## 🔄 Workflow (Logic tuần tự)

```
Tạo đơn hàng (created_at)
        ↓
    [PENDING] ←── 15 phút chờ xác nhận ──→ [AUTO CANCEL]
        ↓ (Admin xác nhận - set confirmed_at)
    [CONFIRMED] ←── 15 phút chờ thanh toán ──→ [AUTO CANCEL]
        ↓ (Khách hàng thanh toán)
    [PAID] → [PREPARING] → [SHIPPING] → [DELIVERED]
```

### ⚠️ Lưu ý quan trọng về logic:

1. **Giai đoạn 1**: Đơn hàng mới tạo có 15 phút để được admin xác nhận
2. **Giai đoạn 2**: Chỉ sau khi được xác nhận, mới bắt đầu đếm 15 phút chờ thanh toán
3. **Không song song**: Hai countdown không chạy đồng thời, mà tuần tự theo workflow

### 📋 Trạng thái hiển thị:

-   **PENDING**: "Chờ xác nhận - còn X phút"
-   **CONFIRMED**: "Đã xác nhận - Chờ thanh toán - còn X phút"
-   **VNPay**: Logic riêng cho thanh toán trực tuyến

## ⚠️ Lưu ý quan trọng

1. **Backup trước khi deploy**: Đảm bảo có backup database
2. **Test trên staging**: Kiểm tra kỹ logic trước khi lên production
3. **Monitor logs**: Theo dõi log để phát hiện vấn đề sớm
4. **Cron job**: Đảm bảo cron job chạy đúng lịch
5. **Timezone**: Kiểm tra timezone của server và application

## 🚀 Deployment Checklist

-   [ ] Chạy migration: `php artisan migrate`
-   [ ] Test command: `php artisan orders:auto-cancel --dry-run`
-   [ ] Thiết lập cron job
-   [ ] Kiểm tra timezone
-   [ ] Monitor logs trong 24h đầu
-   [ ] Thông báo team về thay đổi workflow
