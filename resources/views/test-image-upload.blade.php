<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Upload Hình Ảnh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Test Upload Hình Ảnh</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                                @if(session('image_path'))
                                    <br><strong>Đường dẫn:</strong> {{ session('image_path') }}
                                    <br><strong>URL:</strong> {{ session('image_url') }}
                                    <br><img src="{{ session('image_url') }}" style="max-width: 200px; margin-top: 10px;" class="img-thumbnail">
                                @endif
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('test.image.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="image" class="form-label">Chọn hình ảnh</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>

                        <hr>

                        <h5>Kiểm tra đường dẫn storage</h5>
                        <p><strong>Storage link exists:</strong> {{ file_exists(public_path('storage')) ? 'Yes' : 'No' }}</p>
                        <p><strong>Storage directory:</strong> {{ storage_path('app/public') }}</p>
                        <p><strong>Public storage:</strong> {{ public_path('storage') }}</p>
                        
                        <h5>Test hình ảnh có sẵn</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p>No-image fallback:</p>
                                <img src="{{ asset('images/no-image.svg') }}" style="width: 100px;" class="img-thumbnail">
                            </div>
                            <div class="col-md-6">
                                <p>Test product image:</p>
                                <img src="{{ asset('images/test-product.svg') }}" style="width: 100px;" class="img-thumbnail">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
