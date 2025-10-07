<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\StatisticsController as AdminStatisticsController;

// Route trang chủ
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Routes cho authentication
Route::middleware('guest')->group(function () {
    // Đăng ký
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Đăng nhập
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Quên mật khẩu
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    
    // Reset mật khẩu
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Routes yêu cầu đăng nhập
Route::middleware('auth')->group(function () {
    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard user thường
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Routes dành cho admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Quản lý users
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    
    // Bulk actions và export
    Route::post('/users/bulk-action', [AdminController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('/users/export', [AdminController::class, 'exportUsers'])->name('users.export');
    
    // Quản lý danh mục
    Route::resource('categories', AdminCategoryController::class);
    Route::post('/categories/{category}/toggle-active', [AdminCategoryController::class, 'toggleActive'])->name('categories.toggle-active');
    
    // Quản lý sản phẩm  
    Route::resource('products', AdminProductController::class);
    Route::post('/products/{product}/toggle-active', [AdminProductController::class, 'toggleActive'])->name('products.toggle-active');
    
    // Quản lý đơn hàng
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
    Route::post('/orders/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/update-payment', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.update-payment');
    Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    
    // Quản lý mã giảm giá
    Route::resource('coupons', AdminCouponController::class);
    Route::post('/coupons/{coupon}/toggle-active', [AdminCouponController::class, 'toggleActive'])->name('coupons.toggle-active');
    
    // Quản lý thanh toán
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/statistics', [AdminPaymentController::class, 'statistics'])->name('payments.statistics');
    Route::get('/payments/export', [AdminPaymentController::class, 'export'])->name('payments.export');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/mark-paid', [AdminPaymentController::class, 'markAsPaid'])->name('payments.mark-paid');
    Route::post('/payments/{payment}/mark-failed', [AdminPaymentController::class, 'markAsFailed'])->name('payments.mark-failed');
    
    // Thống kê
    Route::get('/statistics', [AdminStatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/statistics/products', [AdminStatisticsController::class, 'products'])->name('statistics.products');
    Route::get('/statistics/customers', [AdminStatisticsController::class, 'customers'])->name('statistics.customers');
    Route::get('/statistics/chart-data', [AdminStatisticsController::class, 'chartData'])->name('statistics.chart-data');
});

// Routes thanh toán
Route::middleware('auth')->group(function () {
    // VNPay
    Route::post('/payment/vnpay/{order}', [PaymentController::class, 'createVNPayPayment'])->name('payment.vnpay');
    Route::get('/payment/vnpay/callback', [PaymentController::class, 'vnpayCallback'])->name('payment.vnpay.callback');
    
    // Thanh toán khi nhận hàng
    Route::post('/payment/cash/{order}', [PaymentController::class, 'cashOnDelivery'])->name('payment.cash');
});
