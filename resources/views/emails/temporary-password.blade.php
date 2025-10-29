<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mật khẩu tạm thời</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111827; }
        .container { max-width: 560px; margin: 0 auto; padding: 24px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; }
        .pw { font-family: Consolas, Monaco, 'Courier New', monospace; background: #f3f4f6; padding: 8px 12px; border-radius: 6px; display: inline-block; }
        .note { color: #6b7280; font-size: 14px; }
        .brand { font-weight: bold; }
    </style>
    </head>
<body>
    <div class="container">
        <div class="card">
            <p>Xin chào {{ $user->name ?? 'bạn' }},</p>
            <p>MixiShop đã tạo <strong>mật khẩu tạm thời</strong> cho tài khoản của bạn:</p>
            <p class="pw">{{ $temporaryPassword }}</p>
            <p class="note">Vì lý do bảo mật, vui lòng đăng nhập và thay đổi mật khẩu ngay sau khi sử dụng mật khẩu tạm thời này.</p>
            <p>Nếu bạn không yêu cầu, bạn có thể bỏ qua email này.</p>
            <p>Trân trọng,<br /><span class="brand">MixiShop</span></p>
        </div>
    </div>
</body>
</html>


