<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - MixiShop</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header .logo {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .content {
            padding: 40px 30px;
            color: #333;
        }
        .content h2 {
            color: #ff6b6b;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #ff6b6b;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer a {
            color: #ff6b6b;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🍔</div>
            <h1>MixiShop</h1>
            <p>Đồ ăn nhanh ngon miệng</p>
        </div>
        
        <div class="content">
            <h2>Đặt lại mật khẩu</h2>
            <p>Xin chào,</p>
            <p>Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại MixiShop.</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $url }}" class="btn">Đặt lại mật khẩu</a>
            </div>
            
            <div class="info-box">
                <strong>⚠️ Lưu ý quan trọng:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Link này sẽ hết hạn sau 60 phút</li>
                    <li>Chỉ sử dụng link này một lần duy nhất</li>
                    <li>Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này</li>
                </ul>
            </div>
            
            <p>Nếu nút "Đặt lại mật khẩu" không hoạt động, hãy sao chép và dán đường link sau vào trình duyệt:</p>
            <p style="word-break: break-all; background: #f8f9fa; padding: 15px; border-radius: 4px; font-family: monospace;">
                {{ $url }}
            </p>
            
            <p style="margin-top: 30px;">
                Trân trọng,<br>
                <strong>Đội ngũ MixiShop</strong>
            </p>
        </div>
        
        <div class="footer">
            <p>
                Email này được gửi từ <strong>MixiShop</strong><br>
                Nếu bạn có thắc mắc, vui lòng liên hệ: 
                <a href="mailto:support@mixishop.com">support@mixishop.com</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                © {{ date('Y') }} MixiShop. Tất cả quyền được bảo lưu.
            </p>
        </div>
    </div>
</body>
</html>
