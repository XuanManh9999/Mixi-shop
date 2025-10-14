# Hướng dẫn cấu hình MySQL cho MixiShop

## 1. Cài đặt MySQL

### Windows:

-   Tải và cài đặt XAMPP hoặc MySQL Server
-   Hoặc sử dụng MySQL trong WAMP/MAMP

### Tạo database:

```sql
CREATE DATABASE mixishop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 2. Cấu hình file .env

Mở file `.env` và cập nhật thông tin database:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mixishop
DB_USERNAME=root
DB_PASSWORD=

# Hoặc nếu có password
DB_PASSWORD=your_mysql_password
```

## 3. File .env hoàn chỉnh

```env
APP_NAME=MixiShop
APP_ENV=local
APP_KEY=base64:your-generated-key-here
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

# MySQL Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mixishop
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=file

# Cloudinary
CLOUDINARY_CLOUD_NAME=dpbo17rbt
CLOUDINARY_API_KEY=923369223654775
CLOUDINARY_API_SECRET=7szIKlRno-q8XTeuFIy2YIeLuZ4
CLOUDINARY_SECURE=true
CLOUDINARY_FOLDER=mixishop

# VNPay
VNPAY_TMN_CODE=58X4B4HP
VNPAY_SECRET_KEY=VRLDWNVWDNPCOEPBZUTWSEDQAGXJCNGZ
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://localhost:8000/payment/vnpay/callback

# Mail (nếu cần)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="MixiShop"
```

## 4. Chạy migration

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Chạy migration
php artisan migrate

# Seed data (nếu có)
php artisan db:seed
```

## 5. Kiểm tra kết nối

```bash
# Test database connection
php artisan tinker
# Trong tinker:
DB::connection()->getPdo();
```

## 6. Nếu gặp lỗi

### Lỗi "Access denied":

-   Kiểm tra username/password MySQL
-   Đảm bảo MySQL service đang chạy

### Lỗi "Database does not exist":

```sql
CREATE DATABASE mixishop;
```

### Lỗi "Connection refused":

-   Kiểm tra MySQL đang chạy trên port 3306
-   Kiểm tra firewall

## 7. Tạo user MySQL (tùy chọn)

```sql
CREATE USER 'mixishop'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON mixishop.* TO 'mixishop'@'localhost';
FLUSH PRIVILEGES;
```

Sau đó cập nhật .env:

```env
DB_USERNAME=mixishop
DB_PASSWORD=password123
```

## 8. Backup/Restore

### Backup:

```bash
mysqldump -u root -p mixishop > mixishop_backup.sql
```

### Restore:

```bash
mysql -u root -p mixishop < mixishop_backup.sql
```
