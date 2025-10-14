# Hướng dẫn cấu hình Cloudinary

## 1. Thêm vào file .env

Mở file `.env` và thêm các dòng sau:

```env
# Cloudinary Configuration
CLOUDINARY_CLOUD_NAME=dpbo17rbt
CLOUDINARY_API_KEY=923369223654775
CLOUDINARY_API_SECRET=7szIKlRno-q8XTeuFIy2YIeLuZ4
CLOUDINARY_SECURE=true
CLOUDINARY_FOLDER=mixishop
CLOUDINARY_QUALITY=auto
CLOUDINARY_FORMAT=auto
```

## 2. Kiểm tra APP_KEY

Đảm bảo file `.env` có APP_KEY:

```bash
php artisan key:generate
```

## 3. Clear cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## 4. Test Cloudinary

Truy cập: `http://localhost:8000/test-cloudinary-upload`

## 5. Nếu vẫn lỗi

1. Kiểm tra file `.env` có tồn tại không
2. Kiểm tra quyền truy cập file `.env`
3. Restart server: `php artisan serve`

## 6. Cấu trúc file .env mẫu

```env
APP_NAME=MixiShop
APP_ENV=local
APP_KEY=base64:your-generated-key-here
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Cloudinary
CLOUDINARY_CLOUD_NAME=dpbo17rbt
CLOUDINARY_API_KEY=923369223654775
CLOUDINARY_API_SECRET=7szIKlRno-q8XTeuFIy2YIeLuZ4
CLOUDINARY_SECURE=true
CLOUDINARY_FOLDER=mixishop

# VNPay (nếu có)
VNPAY_TMN_CODE=58X4B4HP
VNPAY_SECRET_KEY=VRLDWNVWDNPCOEPBZUTWSEDQAGXJCNGZ
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://localhost:8000/payment/vnpay/callback
```
