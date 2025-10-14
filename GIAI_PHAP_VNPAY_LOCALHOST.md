# Giải Pháp VNPay Callback Cho Localhost

## Vấn Đề

VNPay sandbox không thể gọi callback về `localhost:8000` vì:

-   VNPay server không thể truy cập localhost của máy developer
-   Callback URL cần phải là public URL có thể truy cập từ internet

## Giải Pháp

### 1. Sử Dụng Ngrok (Khuyến nghị)

**Cài đặt Ngrok:**

```bash
# Download từ https://ngrok.com/
# Hoặc sử dụng chocolatey
choco install ngrok

# Hoặc npm
npm install -g ngrok
```

**Chạy Ngrok:**

```bash
# Mở terminal mới và chạy
ngrok http 8000
```

**Cập nhật cấu hình:**

```env
# Trong file .env, thay đổi
VNPAY_RETURN_URL=https://abc123.ngrok.io/payment/vnpay/callback
```

### 2. Sử Dụng LocalTunnel

**Cài đặt:**

```bash
npm install -g localtunnel
```

**Chạy:**

```bash
lt --port 8000 --subdomain mixishop
```

**Cập nhật:**

```env
VNPAY_RETURN_URL=https://mixishop.loca.lt/payment/vnpay/callback
```

### 3. Mô Phỏng Callback (Để Test)

Tôi đã tạo route mô phỏng:

```
GET /simulate-vnpay-success/{order}
```

**Cách sử dụng:**

1. Đặt hàng với VNPay
2. Trên trang thank you, click "Mô phỏng thành công"
3. Hệ thống sẽ cập nhật payment thành công

### 4. Deploy Lên Server Public

**Các option:**

-   Heroku (free tier)
-   Railway
-   Vercel
-   DigitalOcean
-   AWS EC2

## Hướng Dẫn Chi Tiết Ngrok

### Bước 1: Cài Đặt

```bash
# Windows (PowerShell as Admin)
choco install ngrok

# Hoặc download từ https://ngrok.com/download
```

### Bước 2: Đăng Ký Account

```bash
# Đăng ký tại https://dashboard.ngrok.com/signup
# Lấy authtoken và chạy:
ngrok authtoken YOUR_AUTHTOKEN
```

### Bước 3: Chạy Tunnel

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Ngrok tunnel
ngrok http 8000
```

### Bước 4: Cập Nhật Config

```env
# Copy URL từ ngrok (ví dụ: https://abc123.ngrok.io)
VNPAY_RETURN_URL=https://abc123.ngrok.io/payment/vnpay/callback
```

### Bước 5: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

## Test Workflow

### Với Ngrok:

1. Chạy `php artisan serve`
2. Chạy `ngrok http 8000`
3. Cập nhật `VNPAY_RETURN_URL` với ngrok URL
4. Clear cache Laravel
5. Test thanh toán VNPay
6. VNPay sẽ callback về ngrok URL → localhost

### Với Mô Phỏng:

1. Đặt hàng VNPay (sẽ tạo payment pending)
2. Trên trang thank you, click "Mô phỏng thành công"
3. Xem kết quả thanh toán thành công

## Production Setup

### Cấu Hình Production:

```env
# Production
VNPAY_URL=https://pay.vnpay.vn/vpcpay.html
VNPAY_RETURN_URL=https://yourdomain.com/payment/vnpay/callback

# Sandbox
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=https://yourdomain.com/payment/vnpay/callback
```

## Troubleshooting

### Ngrok Issues:

-   **Tunnel not found**: Restart ngrok
-   **Too many connections**: Upgrade ngrok plan
-   **HTTPS required**: Ngrok provides HTTPS by default

### VNPay Issues:

-   **Invalid signature**: Check SECRET_KEY
-   **Invalid return URL**: Ensure URL is accessible
-   **Timeout**: Check server response time

## Monitoring

### Log VNPay Callbacks:

```bash
# Theo dõi log realtime
tail -f storage/logs/laravel.log | grep VNPay
```

### Debug Routes:

-   `/test-vnpay-callback` - Test callback endpoint
-   `/test-vnpay` - Test form
-   `/simulate-vnpay-success/{order}` - Mô phỏng thành công

## Kết Luận

**Để development:**

-   Sử dụng Ngrok hoặc LocalTunnel
-   Hoặc sử dụng route mô phỏng để test

**Để production:**

-   Deploy lên server public
-   Cấu hình domain name
-   Update VNPay return URL

Hệ thống VNPay đã sẵn sàng, chỉ cần giải quyết vấn đề callback URL!
