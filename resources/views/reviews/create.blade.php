@extends('layouts.storefront')

@section('title', 'Đánh giá đơn hàng #' . $order->id)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-star me-2"></i>Đánh giá đơn hàng #{{ $order->id }}
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($orderItems->count() > 0)
                        <form action="{{ route('reviews.store', $order) }}" method="POST" enctype="multipart/form-data" id="reviewForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Chọn sản phẩm cần đánh giá:</label>
                                @if($orderItems->count() > 1)
                                    <select name="product_id" id="product_id" class="form-select" required>
                                        <option value="">-- Chọn sản phẩm --</option>
                                        @foreach($orderItems as $item)
                                            <option value="{{ $item->product_id }}" 
                                                    data-order-item-id="{{ $item->id }}"
                                                    data-product-name="{{ $item->product_name }}"
                                                    {{ isset($selectedProductId) && $selectedProductId == $item->product_id ? 'selected' : '' }}>
                                                {{ $item->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="order_item_id" id="order_item_id">
                                @else
                                    @php $item = $orderItems->first(); @endphp
                                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                    <input type="hidden" name="order_item_id" value="{{ $item->id }}">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Sản phẩm: <strong>{{ $item->product_name }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Đánh giá số sao: <span class="text-danger">*</span></label>
                                <div class="rating-input">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" required>
                                        <label for="rating{{ $i }}" class="star-label">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted" id="rating-text">Chọn số sao đánh giá</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Nhận xét của bạn:</label>
                                <div id="editor-container" style="min-height: 300px; background: #fff; border: 1px solid #dee2e6; border-radius: 0.375rem;"></div>
                                <textarea name="comment" id="comment" style="display: none;"></textarea>
                                <small class="text-muted">Bạn có thể chèn ảnh trực tiếp vào nội dung bằng cách click vào biểu tượng ảnh trên thanh công cụ</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi đánh giá
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Tất cả sản phẩm trong đơn hàng này đã được đánh giá.
                        </div>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 5px;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input .star-label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.rating-input input[type="radio"]:checked ~ .star-label,
.rating-input .star-label:hover,
.rating-input .star-label:hover ~ .star-label {
    color: #ffc107;
}

.rating-input input[type="radio"]:checked ~ .star-label {
    color: #ffc107;
}

/* Quill Editor Styles */
#editor-container .ql-editor {
    min-height: 250px;
    font-size: 14px;
}

#editor-container .ql-editor img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 10px 0;
}

#editor-container .ql-editor p {
    margin-bottom: 10px;
}
</style>
@endpush

@push('scripts')
<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cập nhật order_item_id khi chọn sản phẩm
    const productSelect = document.getElementById('product_id');
    const orderItemInput = document.getElementById('order_item_id');
    
    if (productSelect) {
        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (orderItemInput) {
                orderItemInput.value = selectedOption.dataset.orderItemId || '';
            }
        });
    }

    // Rating text
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingText = document.getElementById('rating-text');
    const ratingTexts = {
        1: 'Rất không hài lòng',
        2: 'Không hài lòng',
        3: 'Bình thường',
        4: 'Hài lòng',
        5: 'Rất hài lòng'
    };

    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (ratingText) {
                ratingText.textContent = ratingTexts[this.value] || 'Chọn số sao đánh giá';
            }
        });
    });

    // Initialize Quill Editor
    const quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Chia sẻ trải nghiệm của bạn về sản phẩm này... Bạn có thể chèn ảnh bằng cách click vào biểu tượng ảnh trên thanh công cụ.',
        modules: {
            toolbar: {
                container: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image'],
                    [{ 'align': [] }],
                    ['clean']
                ],
                handlers: {
                    'image': function() {
                        const input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');
                        input.click();

                        input.onchange = function() {
                            const file = input.files[0];
                            if (!file) return;

                            // Validate file size (5MB)
                            if (file.size > 5 * 1024 * 1024) {
                                alert('Kích thước ảnh không được vượt quá 5MB');
                                return;
                            }

                            // Validate file type
                            if (!file.type.match('image.*')) {
                                alert('Vui lòng chọn file ảnh');
                                return;
                            }

                            // Show loading
                            const range = quill.getSelection(true);
                            quill.insertText(range.index, 'Đang xử lý ảnh...', 'user');
                            quill.setSelection(range.index + 15);

                            // Convert to base64
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                // Remove loading text
                                quill.deleteText(range.index, 15);
                                
                                // Resize image if too large (max 800px width, quality 0.8)
                                const img = new Image();
                                img.onload = function() {
                                    const canvas = document.createElement('canvas');
                                    let width = img.width;
                                    let height = img.height;
                                    
                                    // Resize if width > 800px
                                    if (width > 800) {
                                        height = (height * 800) / width;
                                        width = 800;
                                    }
                                    
                                    canvas.width = width;
                                    canvas.height = height;
                                    
                                    const ctx = canvas.getContext('2d');
                                    ctx.drawImage(img, 0, 0, width, height);
                                    
                                    // Convert to base64 with quality 0.8
                                    const base64 = canvas.toDataURL('image/jpeg', 0.8);
                                    
                                    // Insert base64 image into editor
                                    quill.insertEmbed(range.index, 'image', base64, 'user');
                                };
                                img.onerror = function() {
                                    quill.deleteText(range.index, 15);
                                    alert('Không thể xử lý ảnh. Vui lòng thử lại.');
                                };
                                img.src = e.target.result;
                            };
                            reader.onerror = function() {
                                quill.deleteText(range.index, 15);
                                alert('Có lỗi xảy ra khi đọc file. Vui lòng thử lại.');
                            };
                            reader.readAsDataURL(file);
                        };
                    }
                }
            }
        }
    });

    // Update hidden textarea before form submit
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            const commentTextarea = document.getElementById('comment');
            if (commentTextarea) {
                // Get HTML content from Quill
                const htmlContent = quill.root.innerHTML;
                // Check if content is not empty (more than just <p><br></p>)
                const textContent = quill.getText().trim();
                if (textContent.length === 0 && htmlContent === '<p><br></p>') {
                    // Allow empty comment
                    commentTextarea.value = '';
                } else {
                    commentTextarea.value = htmlContent;
                }
            }
        });
    }
});
</script>
@endpush
