<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CSRF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Test CSRF Token</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('test.csrf.post') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="message" class="form-label">Tin nhắn test</label>
                                <input type="text" class="form-control" id="message" name="message" value="Hello CSRF!" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Gửi (Test CSRF)</button>
                        </form>

                        <hr>

                        <div class="mt-3">
                            <h6>Thông tin debug:</h6>
                            <ul>
                                <li><strong>CSRF Token:</strong> <code>{{ csrf_token() }}</code></li>
                                <li><strong>Session ID:</strong> <code>{{ session()->getId() }}</code></li>
                                <li><strong>Session Driver:</strong> <code>{{ config('session.driver') }}</code></li>
                                <li><strong>APP_KEY exists:</strong> {{ config('app.key') ? 'Yes' : 'No' }}</li>
                            </ul>
                        </div>

                        <div class="mt-3">
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary">Về trang chủ</a>
                            <a href="{{ url('/test-cloudinary-upload') }}" class="btn btn-outline-primary">Test Cloudinary</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
