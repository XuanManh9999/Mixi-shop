<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Cloudinary Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Test Cloudinary Upload</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                                @if(session('result'))
                                    <hr>
                                    <h6>Chi tiết upload:</h6>
                                    <ul>
                                        <li><strong>Public ID:</strong> {{ session('result')['public_id'] }}</li>
                                        <li><strong>Secure URL:</strong> <a href="{{ session('result')['secure_url'] }}" target="_blank">{{ session('result')['secure_url'] }}</a></li>
                                        <li><strong>Format:</strong> {{ session('result')['format'] }}</li>
                                        <li><strong>Size:</strong> {{ session('result')['width'] }}x{{ session('result')['height'] }}</li>
                                        <li><strong>Bytes:</strong> {{ number_format(session('result')['bytes']) }} bytes</li>
                                    </ul>
                                    <div class="mt-3">
                                        <img src="{{ session('result')['secure_url'] }}" style="max-width: 300px;" class="img-thumbnail">
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('test.cloudinary.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="image" class="form-label">Chọn hình ảnh</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload lên Cloudinary</button>
                        </form>

                        <hr>

                        <h5>Thông tin Cloudinary</h5>
                        <ul>
                            <li><strong>Cloud Name:</strong> {{ config('cloudinary.cloud_name') }}</li>
                            <li><strong>Folder:</strong> {{ config('cloudinary.folder') }}</li>
                            <li><strong>Secure:</strong> {{ config('cloudinary.secure') ? 'Yes' : 'No' }}</li>
                        </ul>

                        <div class="mt-3">
                            <a href="{{ route('test.image.show') }}" class="btn btn-outline-secondary">Test Local Upload</a>
                            <a href="{{ url('/admin/products/create') }}" class="btn btn-outline-primary">Tạo sản phẩm mới</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
