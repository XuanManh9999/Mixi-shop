# Sửa lỗi Countdown Timer - MixiShop

## 🐛 Vấn đề đã phát hiện

### **Bug**: Nhiều countdown timer chạy đồng thời

-   Đơn hàng VNPay với status "confirmed" có 2 countdown:
    1. **VNPay countdown** (từ created_at)
    2. **Confirmed countdown** (từ confirmed_at)
-   Gây nhầm lẫn cho người dùng với 2 thời gian khác nhau

### **Ví dụ lỗi**:

```
Xác nhận lúc: 14/10/2025 17:03:09
Thời gian còn lại: 11:39 (VNPay)
Thời gian còn lại: 11:55 (Confirmed)
```

## ✅ Giải pháp đã áp dụng

### **Logic ưu tiên mới**:

1. **VNPay có ưu tiên cao nhất** - chỉ hiển thị VNPay countdown
2. **Confirmed countdown** - chỉ hiển thị khi KHÔNG phải VNPay
3. **Pending countdown** - chỉ khi status = pending

### **Thứ tự kiểm tra**:

```php
@if($order->canPayVNPay())
    // Hiển thị VNPay countdown (từ created_at)
@elseif($order->status === 'confirmed' && $order->payment_status === 'unpaid')
    // Hiển thị Confirmed countdown (từ confirmed_at)
@elseif($order->status === 'pending')
    // Hiển thị Pending countdown (từ created_at)
@endif
```

## 🔄 Workflow sau khi sửa

### **Đơn hàng VNPay**:

```
Tạo đơn → [PENDING + Pending countdown] → Admin xác nhận → [CONFIRMED + VNPay countdown]
              ↓                                                    ↓
         (từ created_at)                                    (từ created_at)
         Chờ xác nhận 15 phút                               Chờ thanh toán 15 phút
```

### **Đơn hàng thường (COD)**:

```
Tạo đơn → [PENDING + Pending countdown] → Admin xác nhận → [CONFIRMED + Confirmed countdown]
              ↓                                                    ↓
         (từ created_at)                                    (từ confirmed_at)
```

## 📋 Thay đổi cụ thể

### **Frontend (Blade)**:

-   Sử dụng `@if/@elseif` thay vì `@if/@endif` riêng lẻ
-   VNPay countdown có ưu tiên cao nhất
-   Hiển thị thời điểm tham chiếu rõ ràng

### **JavaScript**:

-   Chỉ khởi tạo 1 countdown timer duy nhất
-   Tránh conflict giữa các timer

### **Hiển thị thời gian**:

-   **VNPay**: "Tạo đơn lúc: [created_at]"
-   **Confirmed**: "Xác nhận lúc: [confirmed_at]"
-   **Pending**: "Tạo đơn lúc: [created_at]"

## 🎯 Kết quả

### **Trước (có bug)**:

-   2 countdown chạy đồng thời
-   Thời gian không nhất quán
-   Gây nhầm lẫn cho user

### **Sau (đã sửa)**:

-   ✅ Chỉ 1 countdown duy nhất
-   ✅ Thời gian nhất quán
-   ✅ Logic rõ ràng theo workflow

## 🧪 Test Cases

### **Test 1: Đơn VNPay mới tạo**

-   Status: pending
-   Payment: vnpay
-   Kết quả: Hiển thị VNPay countdown từ created_at

### **Test 2: Đơn VNPay đã xác nhận**

-   Status: confirmed
-   Payment: vnpay
-   Kết quả: Vẫn hiển thị VNPay countdown từ created_at

### **Test 3: Đơn COD đã xác nhận**

-   Status: confirmed
-   Payment: cod
-   Kết quả: Hiển thị Confirmed countdown từ confirmed_at

### **Test 4: Đơn thường chờ xác nhận**

-   Status: pending
-   Payment: cod
-   Kết quả: Hiển thị Pending countdown từ created_at

## ⚠️ Lưu ý quan trọng

1. **VNPay luôn ưu tiên**: Dù status là gì, VNPay countdown luôn được hiển thị
2. **Thời gian tham chiếu**:
    - VNPay: từ created_at (15 phút)
    - Confirmed: từ confirmed_at (15 phút)
    - Pending: từ created_at (15 phút)
3. **Không conflict**: Chỉ 1 countdown active tại 1 thời điểm
