# MixiShop - Hệ thống Authentication

## Tổng quan

Đã tạo thành công hệ thống đăng nhập, đăng ký và quên mật khẩu cho ứng dụng MixiShop.

## Các tính năng đã implement:

### 1. Đăng ký tài khoản

-   URL: `/register`
-   Form gồm: Họ tên, Email, Số điện thoại (tùy chọn), Mật khẩu, Xác nhận mật khẩu
-   Validation đầy đủ với thông báo tiếng Việt
-   Tự động đăng nhập sau khi đăng ký thành công

### 2. Đăng nhập

-   URL: `/login`
-   Form gồm: Email, Mật khẩu, Ghi nhớ đăng nhập
-   Validation và xử lý lỗi
-   Chuyển hướng đến dashboard sau khi đăng nhập

### 3. Quên mật khẩu

-   URL: `/forgot-password`
-   Gửi email reset mật khẩu
-   URL: `/reset-password/{token}` - Form đặt lại mật khẩu

### 4. Dashboard

-   Trang chính sau khi đăng nhập
-   Hiển thị thông tin user
-   Menu dropdown với tùy chọn đăng xuất

## Cấu hình cần thiết:

### 1. Database

Cập nhật file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mixishop
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 2. Email (cho chức năng quên mật khẩu)

Cấu hình SMTP trong `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="MixiShop"
```

### 3. Chạy migration

```bash
php artisan migrate
```

## Các file đã tạo/chỉnh sửa:

### Controllers:

-   `app/Http/Controllers/AuthController.php` - Xử lý authentication

### Models:

-   `app/Models/User.php` - Cập nhật fillable fields

### Routes:

-   `routes/web.php` - Thêm authentication routes

### Views:

-   `resources/views/layouts/app.blade.php` - Layout chung
-   `resources/views/auth/login.blade.php` - Form đăng nhập
-   `resources/views/auth/register.blade.php` - Form đăng ký
-   `resources/views/auth/forgot-password.blade.php` - Form quên mật khẩu
-   `resources/views/auth/reset-password.blade.php` - Form reset mật khẩu
-   `resources/views/dashboard.blade.php` - Trang dashboard
-   `resources/views/welcome.blade.php` - Cập nhật trang chủ

### Migrations:

-   `database/migrations/0001_01_01_000002_create_password_reset_tokens_table.php`

## Routes có sẵn:

### Guest routes:

-   `GET /` - Trang chủ
-   `GET /login` - Form đăng nhập
-   `POST /login` - Xử lý đăng nhập
-   `GET /register` - Form đăng ký
-   `POST /register` - Xử lý đăng ký
-   `GET /forgot-password` - Form quên mật khẩu
-   `POST /forgot-password` - Gửi email reset
-   `GET /reset-password/{token}` - Form reset mật khẩu
-   `POST /reset-password` - Xử lý reset mật khẩu

### Authenticated routes:

-   `GET /dashboard` - Dashboard
-   `POST /logout` - Đăng xuất

## Giao diện:

-   Thiết kế responsive với Bootstrap 5
-   Gradient background đẹp mắt
-   Icons Font Awesome
-   Theme màu cam-đỏ phù hợp với MixiShop
-   Form validation với thông báo lỗi tiếng Việt

## Bảo mật:

-   CSRF protection
-   Password hashing với bcrypt
-   Session management
-   Input validation
-   Middleware authentication

Hệ thống authentication đã hoàn thiện và sẵn sàng sử dụng!
