@extends('layouts.admin')

@section('title', 'Chi tiết Đơn hàng - Admin MixiShop')
@section('page-title', 'Chi tiết Đơn hàng')
@section('page-description', 'Đơn hàng #' . $order->id)

@section('content')
<div class="row">
    <!-- Order Info -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông Tin Đơn Hàng</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Mã đơn:</strong></td>
                        <td>#{{ $order->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Trạng thái:</strong></td>
                        <td>
                            <span class="badge bg-{{ $order->status_color }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Thanh toán:</strong></td>
                        <td>
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @elseif($order->payment_status === 'pending')
                                <span class="badge bg-warning">Chờ thanh toán</span>
                            @else
                                <span class="badge bg-danger">{{ $order->payment_status }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Phương thức:</strong></td>
                        <td>{{ $order->payment_method_label }}</td>
                    </tr>
                    <tr>
                        <td><strong>Ngày đặt:</strong></td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @if($order->placed_at)
                    <tr>
                        <td><strong>Ngày đặt:</strong></td>
                        <td>{{ $order->placed_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                </table>
                
                <!-- Update Status -->
                <div class="mt-3">
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                        @csrf
                        <div class="mb-2">
                            <label for="status" class="form-label">Cập nhật trạng thái:</label>
                            <select class="form-select form-select-sm" name="status">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Đang chuẩn bị</option>
                                <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-save me-1"></i>Cập nhật
                        </button>
                    </form>
                </div>
                
                <!-- Update Payment Status -->
                <div class="mt-3">
                    <form method="POST" action="{{ route('admin.orders.update-payment', $order) }}">
                        @csrf
                        <div class="mb-2">
                            <label for="payment_status" class="form-label">Trạng thái thanh toán:</label>
                            <select class="form-select form-select-sm" name="payment_status">
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Thất bại</option>
                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Hoàn tiền</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-credit-card me-1"></i>Cập nhật TT
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Customer Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Thông Tin Khách Hàng</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Tên:</strong></td>
                        <td>{{ $order->ship_full_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $order->user->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>SĐT:</strong></td>
                        <td>{{ $order->ship_phone }}</td>
                    </tr>
                    <tr>
                        <td><strong>Địa chỉ:</strong></td>
                        <td>
                            {{ $order->ship_address }}<br>
                            {{ $order->ship_ward }}, {{ $order->ship_district }}<br>
                            {{ $order->ship_city }}
                        </td>
                    </tr>
                    @if($order->note)
                    <tr>
                        <td><strong>Ghi chú:</strong></td>
                        <td>{{ $order->note }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <!-- Order Items -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Chi Tiết Đơn Hàng</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product)
                                            <img src="{{ $item->product->main_image }}" 
                                                 class="rounded me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;" 
                                                 alt="{{ $item->product_name }}">
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $item->product_name }}</div>
                                            @if($item->sku)
                                                <small class="text-muted">SKU: {{ $item->sku }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $item->formatted_unit_price }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $item->quantity }}</span>
                                </td>
                                <td>
                                    <strong class="text-success">{{ $item->formatted_total_price }}</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="3" class="text-end"><strong>Tạm tính:</strong></td>
                                <td><strong>{{ number_format($order->subtotal_amount, 0, ',', '.') }}₫</strong></td>
                            </tr>
                            @if($order->shipping_fee > 0)
                            <tr class="table-light">
                                <td colspan="3" class="text-end"><strong>Phí giao hàng:</strong></td>
                                <td><strong>{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</strong></td>
                            </tr>
                            @endif
                            @if($order->discount_amount > 0)
                            <tr class="table-light">
                                <td colspan="3" class="text-end"><strong>Giảm giá:</strong></td>
                                <td><strong class="text-danger">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</strong></td>
                            </tr>
                            @endif
                            <tr class="table-warning">
                                <td colspan="3" class="text-end"><strong>TỔNG CỘNG:</strong></td>
                                <td><strong class="text-success fs-5">{{ $order->formatted_total }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
            <div>
                <button class="btn btn-primary me-2" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>In đơn hàng
                </button>
                <button class="btn btn-info" onclick="exportOrder()">
                    <i class="fas fa-download me-2"></i>Xuất PDF
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportOrder() {
    // Placeholder for PDF export functionality
    alert('Chức năng xuất PDF sẽ được thêm sau!');
}
</script>
@endpush
