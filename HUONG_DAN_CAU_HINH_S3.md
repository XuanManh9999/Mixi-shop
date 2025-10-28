# HƯỚNG DẪN CẤU HÌNH AWS S3 CHO UPLOAD HÌNH ẢNH

## 1. Cài đặt Package (ĐÃ HOÀN TẤT)

```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

## 2. Cấu hình .env

Thêm các thông tin sau vào file `.env`:

```env
# AWS S3 Configuration
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=your-access-key-id
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_DEFAULT_REGION=your-region (ví dụ: ap-southeast-1)
AWS_BUCKET=your-bucket-name
AWS_URL=https://your-bucket-name.s3.your-region.amazonaws.com
AWS_ENDPOINT=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### Giải thích các tham số:

-   **FILESYSTEM_DISK**: Disk mặc định cho upload (đặt là `s3`)
-   **AWS_ACCESS_KEY_ID**: Access Key ID từ AWS IAM
-   **AWS_SECRET_ACCESS_KEY**: Secret Access Key từ AWS IAM
-   **AWS_DEFAULT_REGION**: Region của S3 bucket (ví dụ: `ap-southeast-1`, `us-east-1`)
-   **AWS_BUCKET**: Tên bucket S3 của bạn
-   **AWS_URL**: URL công khai của bucket (tùy chọn, để trống thì Laravel tự generate)
-   **AWS_ENDPOINT**: Endpoint tùy chỉnh (để trống nếu dùng AWS S3 standard)
-   **AWS_USE_PATH_STYLE_ENDPOINT**: Sử dụng path-style endpoint (false cho AWS S3)

### Ví dụ cấu hình thực tế:

```env
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=AKIAIOSFODNN7EXAMPLE
AWS_SECRET_ACCESS_KEY=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=mixishop-products
AWS_URL=https://mixishop-products.s3.ap-southeast-1.amazonaws.com
```

## 3. Cấu hình S3 Bucket

### 3.1. Tạo Bucket trên AWS Console

1. Đăng nhập vào AWS Console
2. Truy cập S3 Service
3. Click "Create bucket"
4. Đặt tên bucket (ví dụ: `mixishop-products`)
5. Chọn Region gần nhất với server/users của bạn
6. **Block Public Access**: Bỏ chọn "Block all public access" nếu muốn ảnh public
7. Click "Create bucket"

### 3.2. Cấu hình Bucket Policy (để ảnh public)

Vào bucket → **Permissions** → **Bucket Policy**, thêm policy sau:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::mixishop-products/*"
        }
    ]
}
```

**Lưu ý**: Thay `mixishop-products` bằng tên bucket của bạn.

### 3.3. Cấu hình CORS (nếu cần truy cập từ frontend)

Vào bucket → **Permissions** → **CORS**, thêm config:

```json
[
    {
        "AllowedHeaders": ["*"],
        "AllowedMethods": ["GET", "HEAD"],
        "AllowedOrigins": ["*"],
        "ExposeHeaders": []
    }
]
```

## 4. Tạo IAM User cho Upload

### 4.1. Tạo IAM User

1. Truy cập IAM Console
2. Click **Users** → **Add users**
3. Đặt tên user (ví dụ: `mixishop-uploader`)
4. Chọn **Access key - Programmatic access**
5. Click **Next**

### 4.2. Gán quyền cho User

Chọn **Attach existing policies directly**, sau đó tạo policy mới:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::mixishop-products",
                "arn:aws:s3:::mixishop-products/*"
            ]
        }
    ]
}
```

### 4.3. Lấy Access Key

-   Sau khi tạo user, AWS sẽ hiển thị **Access Key ID** và **Secret Access Key**
-   **Lưu lại ngay** vì Secret Key chỉ hiện 1 lần duy nhất
-   Điền các giá trị này vào `.env`

## 5. Cấu trúc Upload

Hệ thống sẽ upload theo cấu trúc:

```
s3://mixishop-products/
├── products/
│   ├── product-slug-1/
│   │   ├── product-slug-1_thumb_1234567890.jpg      (thumbnail)
│   │   └── gallery/
│   │       ├── product-slug-1_gallery_1_1234567890.jpg
│   │       ├── product-slug-1_gallery_2_1234567890.jpg
│   │       └── product-slug-1_gallery_3_1234567890.jpg
│   └── product-slug-2/
│       └── ...
```

## 6. Test Upload

Sau khi cấu hình xong:

1. Clear cache config:

```bash
php artisan config:clear
php artisan config:cache
```

2. Thử tạo sản phẩm mới từ Admin Panel
3. Upload thumbnail và gallery images
4. Kiểm tra:
    - Ảnh đã được upload lên S3 bucket
    - URL ảnh hiển thị đúng trên frontend
    - Có thể truy cập URL ảnh từ browser

## 7. Kiểm tra Log

Nếu có lỗi, kiểm tra log tại:

```
storage/logs/laravel.log
```

Các lỗi thường gặp:

-   **InvalidAccessKeyId**: Access Key sai
-   **SignatureDoesNotMatch**: Secret Key sai
-   **NoSuchBucket**: Bucket không tồn tại hoặc region sai
-   **AccessDenied**: IAM user chưa có quyền
-   **403 Forbidden**: Bucket policy chưa cho phép public access

## 8. Code đã được cập nhật

### 8.1. S3Service (app/Services/S3Service.php)

-   `uploadProductThumbnail()`: Upload thumbnail
-   `uploadProductGalleryImage()`: Upload gallery images
-   `delete()`: Xóa file từ S3

### 8.2. ProductController (app/Http/Controllers/Admin/ProductController.php)

-   `store()`: Upload ảnh khi tạo sản phẩm mới
-   `update()`: Upload ảnh khi cập nhật sản phẩm
-   `destroy()`: Xóa ảnh khi xóa sản phẩm

### 8.3. Models

-   `Product::getMainImageAttribute()`: Tự động nhận diện S3 URL
-   `ProductImage::getFullImageUrlAttribute()`: Tự động nhận diện S3 URL

## 9. Migration từ Cloudinary sang S3

Nếu bạn đang có ảnh trên Cloudinary và muốn chuyển sang S3:

1. Giữ nguyên dữ liệu hiện tại (ảnh Cloudinary vẫn hoạt động)
2. Từ nay upload mới sẽ lên S3
3. Nếu muốn migrate toàn bộ:
    - Download ảnh từ Cloudinary
    - Upload lại lên S3
    - Update `thumbnail_url` và `image_url` trong database

## 10. Bảo mật

**LƯU Ý QUAN TRỌNG:**

-   **KHÔNG** commit file `.env` lên Git
-   **KHÔNG** chia sẻ Access Key và Secret Key
-   Nếu key bị lộ, xóa ngay trên AWS Console và tạo key mới
-   Nên sử dụng IAM Role thay vì Access Key nếu deploy trên AWS EC2

## 11. Chi phí S3

-   **Storage**: ~$0.023/GB/tháng (region ap-southeast-1)
-   **Request**:
    -   PUT/POST: $0.005/1000 requests
    -   GET: $0.0004/1000 requests
-   **Data Transfer**:
    -   Upload: Miễn phí
    -   Download: $0.12/GB (sau 1GB đầu tiên)

Ước tính cho 1000 sản phẩm (mỗi sản phẩm ~5MB ảnh):

-   Storage: 5GB × $0.023 = **~$0.12/tháng**
-   Rất rẻ cho dự án nhỏ và vừa!

---

**Hoàn tất!** Hệ thống đã sẵn sàng upload ảnh lên S3. Hãy cấu hình `.env` theo hướng dẫn trên và test thử nhé! 🚀
