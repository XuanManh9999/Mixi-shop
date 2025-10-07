@extends('layouts.admin')

@section('title', 'Chỉnh sửa User - Admin MixiShop')
@section('page-title', 'Chỉnh sửa User')
@section('page-description', 'Cập nhật thông tin user: ' . $user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Chỉnh Sửa Thông Tin User</h5>
                <div>
                    @if($user->is_admin)
                        <span class="badge bg-danger"><i class="fas fa-crown me-1"></i>Admin</span>
                    @else
                        <span class="badge bg-primary"><i class="fas fa-user me-1"></i>User</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Họ và tên <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       placeholder="Nhập họ và tên"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       placeholder="Nhập địa chỉ email"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Số điện thoại
                                </label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" 
                                       value="{{ old('phone', $user->phone) }}" 
                                       placeholder="Nhập số điện thoại">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="is_admin" class="form-label">
                                    <i class="fas fa-shield-alt me-1"></i>Quyền hạn
                                </label>
                                @if($user->id === Auth::id())
                                    <input type="hidden" name="is_admin" value="{{ $user->is_admin }}">
                                    <select class="form-select" disabled>
                                        <option>{{ $user->is_admin ? 'Admin' : 'User thường' }}</option>
                                    </select>
                                    <small class="text-muted">Không thể thay đổi quyền của chính mình</small>
                                @else
                                    <select class="form-select @error('is_admin') is-invalid @enderror" 
                                            id="is_admin" name="is_admin">
                                        <option value="0" {{ (old('is_admin', $user->is_admin) == '0') ? 'selected' : '' }}>
                                            User thường
                                        </option>
                                        <option value="1" {{ (old('is_admin', $user->is_admin) == '1') ? 'selected' : '' }}>
                                            Admin
                                        </option>
                                    </select>
                                @endif
                                @error('is_admin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3">
                        <i class="fas fa-key me-2"></i>Đổi mật khẩu 
                        <small class="text-muted">(để trống nếu không muốn thay đổi)</small>
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Mật khẩu mới
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" 
                                           placeholder="Nhập mật khẩu mới (tối thiểu 8 ký tự)">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Xác nhận mật khẩu mới
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" name="password_confirmation" 
                                           placeholder="Nhập lại mật khẩu mới">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Thông tin user:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>ID:</strong> #{{ $user->id }}</li>
                            <li><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</li>
                            <li><strong>Cập nhật lần cuối:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</li>
                            @if($user->email_verified_at)
                                <li><strong>Email đã xác thực:</strong> {{ $user->email_verified_at->format('d/m/Y H:i') }}</li>
                            @else
                                <li><strong>Email:</strong> <span class="text-warning">Chưa xác thực</span></li>
                            @endif
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                        <div>
                            <button type="reset" class="btn btn-outline-warning me-2">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirm = document.getElementById('password_confirmation');
    
    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    }
    
    if (togglePasswordConfirm && passwordConfirm) {
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirm.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    }
});
</script>
@endpush
