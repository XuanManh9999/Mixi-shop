<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\StatisticsController as AdminStatisticsController;
use App\Http\Controllers\ProductController as FrontProductController;
use App\Http\Controllers\CategoryController as FrontCategoryController;
use App\Http\Controllers\CheckoutController;

// Route trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/suggest', [HomeController::class, 'suggest'])->name('home.suggest');

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
    Route::get('/dashboard', [\App\Http\Controllers\ProfileController::class, 'dashboard'])->name('dashboard');
    
    // Quản lý đơn hàng của user
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [\App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
    
    // Đánh giá đơn hàng
    Route::get('/orders/{order}/review', [\App\Http\Controllers\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/orders/{order}/review', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Quản lý thông tin cá nhân
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'deleteAccount'])->name('profile.delete');
});

// Routes frontend sản phẩm và danh mục
Route::get('/products', [FrontProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [FrontProductController::class, 'show'])->name('products.show');
// Đánh giá sản phẩm (public)
Route::get('/products/{product:slug}/reviews', [\App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
Route::get('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'show'])->name('reviews.show');
// Có thể mở rộng sau: xem theo danh mục
Route::get('/c/{category:slug}', [FrontCategoryController::class, 'show'])->name('categories.show');
// Cart page (render bằng JS + localStorage)
Route::view('/cart', 'storefront.cart')->name('cart.index');

// Checkout - yêu cầu đăng nhập
Route::middleware('auth')->group(function () {
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'place'])->name('checkout.place');
});
Route::get('/checkout/thank-you/{order}', [CheckoutController::class, 'thankyou'])->name('checkout.thankyou');
// Public coupon validate for checkout
Route::get('/coupon/validate', [CheckoutController::class, 'validateCoupon'])->name('coupon.validate');

// Tra cứu đơn hàng (không cần đăng nhập)
Route::get('/track-order', [\App\Http\Controllers\OrderController::class, 'track'])->name('orders.track');
Route::post('/track-order', [\App\Http\Controllers\OrderController::class, 'track']);

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
    Route::post('/payments/{payment}/confirm-order', [AdminPaymentController::class, 'confirmOrder'])->name('payments.confirm-order');
    
    // Thống kê
    Route::get('/statistics', [AdminStatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/statistics/products', [AdminStatisticsController::class, 'products'])->name('statistics.products');
    Route::get('/statistics/products/{product}/orders', [AdminStatisticsController::class, 'productOrders'])->name('statistics.product-orders');
    Route::get('/statistics/customers', [AdminStatisticsController::class, 'customers'])->name('statistics.customers');
    Route::get('/statistics/chart-data', [AdminStatisticsController::class, 'chartData'])->name('statistics.chart-data');
    
    // Lịch sử đăng nhập
    Route::get('/login-history', [\App\Http\Controllers\Admin\LoginHistoryController::class, 'index'])->name('login-history.index');
    Route::get('/login-history/user/{user}', [\App\Http\Controllers\Admin\LoginHistoryController::class, 'userHistory'])->name('login-history.user');
    
    // Quản lý đánh giá
    Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class)->only(['index', 'destroy']);
    Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
    Route::post('/reviews/bulk-action', [\App\Http\Controllers\Admin\ReviewController::class, 'bulkAction'])->name('reviews.bulk-action');
});

// Routes thanh toán VNPay
// ⚠️ QUAN TRỌNG: Đặt routes cụ thể (callback, return) TRƯỚC route có parameter {order}
// để tránh conflict khi Laravel match route
Route::get('/payment/vnpay/callback', [PaymentController::class, 'vnpayCallback'])->name('payment.vnpay.callback');
Route::get('/payment/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
Route::get('/payment/vnpay/{order}', [PaymentController::class, 'createVNPayPayment'])->name('payment.vnpay');

// Alternative callback routes để test
Route::any('/vnpay/callback', [PaymentController::class, 'vnpayCallback'])->name('vnpay.callback.alt');
Route::any('/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return.alt');
Route::any('/callback', [PaymentController::class, 'vnpayCallback'])->name('callback.alt');
    
    // Thanh toán khi nhận hàng
    Route::post('/payment/cash/{order}', [PaymentController::class, 'cashOnDelivery'])->name('payment.cash');

// Debug route để test VNPay callback
Route::get('/test-vnpay-callback', function(\Illuminate\Http\Request $request) {
    return response()->json([
        'message' => 'VNPay callback route is working',
        'all_params' => $request->all(),
        'url' => $request->fullUrl()
    ]);
});

// Test page
Route::get('/test-vnpay', function() {
    return view('test-vnpay');
});

// Test VNPay return route
Route::get('/test-vnpay-return', function() {
    return response()->json([
        'message' => 'VNPay return route is working!',
        'route_name' => 'test-vnpay-return',
        'timestamp' => now()
    ]);
});

// Test PaymentController vnpayReturn method
Route::get('/test-payment-return', [PaymentController::class, 'vnpayReturn']);

// Test Auto Cancel Orders
Route::get('/test-auto-cancel', function() {
    $pendingExpired = \App\Models\Order::where('status', 'pending')
        ->where('created_at', '<=', now()->subMinutes(15))
        ->get();
        
    $confirmedExpired = \App\Models\Order::where('status', 'confirmed')
        ->where('payment_status', 'unpaid')
        ->whereNotNull('confirmed_at')
        ->where('confirmed_at', '<=', now()->subMinutes(15))
        ->get();
        
    return response()->json([
        'pending_expired' => $pendingExpired->count(),
        'confirmed_expired' => $confirmedExpired->count(),
        'pending_orders' => $pendingExpired->map(function($order) {
            return [
                'id' => $order->id,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'minutes_ago' => $order->created_at->diffInMinutes(now()),
                'should_cancel' => $order->isPendingExpired()
            ];
        }),
        'confirmed_orders' => $confirmedExpired->map(function($order) {
            return [
                'id' => $order->id,
                'confirmed_at' => $order->confirmed_at?->format('d/m/Y H:i'),
                'minutes_ago' => $order->confirmed_at?->diffInMinutes(now()),
                'should_cancel' => $order->isConfirmedExpired()
            ];
        })
    ]);
});

// Test Cloudinary Facade
Route::get('/test-cloudinary-facade', function() {
    try {
        $result = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::getUrl('sample');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Cloudinary Facade hoạt động!',
            'sample_url' => $result,
            'config' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key' => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret') ? 'SET' : 'NOT SET'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Lỗi Cloudinary Facade: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test SimpleCloudinaryService
Route::get('/test-simple-cloudinary', function() {
    try {
        $service = app(\App\Services\SimpleCloudinaryService::class);
        
        return response()->json([
            'status' => 'success',
            'message' => 'SimpleCloudinaryService khởi tạo thành công!',
            'service' => 'OK'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Lỗi SimpleCloudinaryService: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test CloudinaryService
Route::get('/test-cloudinary-service', function() {
    try {
        $service = app(\App\Services\CloudinaryService::class);
        
        return response()->json([
            'status' => 'success',
            'message' => 'CloudinaryService khởi tạo thành công!',
            'service' => 'OK'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Lỗi CloudinaryService: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test Cloudinary simple
Route::get('/test-cloudinary-simple', function() {
    try {
        $cloudinary = new \Cloudinary\Cloudinary([
            'cloud' => [
                'cloud_name' => 'dpbo17rbt',
                'api_key' => '923369223654775',
                'api_secret' => '7szIKlRno-q8XTeuFI2YIeLuZ4',
                'secure' => true
            ]
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Cloudinary khởi tạo thành công!',
            'config' => [
                'cloud_name' => 'dpbo17rbt',
                'api_key' => '923369223654775',
                'api_secret' => 'HIDDEN',
                'secure' => true
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Lỗi Cloudinary: ' . $e->getMessage()
        ]);
    }
});

// Test Cloudinary config
Route::get('/test-cloudinary-config', function() {
    return response()->json([
        'cloudinary_config' => [
            'cloud_name' => config('cloudinary.cloud_name'),
            'api_key' => config('cloudinary.api_key'),
            'api_secret' => config('cloudinary.api_secret') ? 'SET' : 'NOT SET',
            'secure' => config('cloudinary.secure'),
            'folder' => config('cloudinary.folder'),
        ],
        'env_values' => [
            'CLOUDINARY_CLOUD_NAME' => env('CLOUDINARY_CLOUD_NAME'),
            'CLOUDINARY_API_KEY' => env('CLOUDINARY_API_KEY'),
            'CLOUDINARY_API_SECRET' => env('CLOUDINARY_API_SECRET') ? 'SET' : 'NOT SET',
            'CLOUDINARY_SECURE' => env('CLOUDINARY_SECURE'),
            'CLOUDINARY_FOLDER' => env('CLOUDINARY_FOLDER'),
        ]
    ]);
});

// Test MySQL connection
Route::get('/test-mysql', function() {
    try {
        $pdo = DB::connection()->getPdo();
        $version = DB::select('SELECT VERSION() as version')[0]->version;
        $database = DB::connection()->getDatabaseName();
        
        return response()->json([
            'status' => 'success',
            'message' => 'MySQL kết nối thành công!',
            'database' => $database,
            'version' => $version,
            'connection' => 'OK'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Lỗi kết nối MySQL: ' . $e->getMessage(),
            'config' => [
                'host' => config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
                'database' => config('database.connections.mysql.database'),
                'username' => config('database.connections.mysql.username')
            ]
        ]);
    }
});

// Test CSRF
Route::get('/test-csrf', function() {
    return view('test-csrf');
})->name('test.csrf.show');

Route::post('/test-csrf', function(\Illuminate\Http\Request $request) {
    return redirect()->back()->with('success', 'CSRF hoạt động tốt! Tin nhắn: ' . $request->message);
})->name('test.csrf.post');

// Test Cloudinary upload
Route::get('/test-cloudinary-upload', function() {
    return view('test-cloudinary-upload');
})->name('test.cloudinary.show');

Route::post('/test-cloudinary-upload', function(\Illuminate\Http\Request $request) {
    try {
        if ($request->hasFile('image')) {
            $cloudinaryService = app(\App\Services\CloudinaryService::class);
            
            $uploadResult = $cloudinaryService->upload(
                $request->file('image'),
                'test'
            );
            
            if ($uploadResult) {
                return redirect()->back()->with([
                    'success' => 'Upload Cloudinary thành công!',
                    'result' => $uploadResult
                ]);
            } else {
                return redirect()->back()->with('error', 'Không thể upload lên Cloudinary');
            }
        }
        
        return redirect()->back()->with('error', 'Không có file được upload');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
    }
})->name('test.cloudinary.upload');

// Test image upload (local)
Route::get('/test-image-upload', function() {
    return view('test-image-upload');
})->name('test.image.show');

Route::post('/test-image-upload', function(\Illuminate\Http\Request $request) {
    try {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'products/' . time() . '_test_' . \Illuminate\Support\Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // Store file
            $stored = $image->storeAs('public', $imageName);
            
            if ($stored) {
                $imagePath = 'storage/' . $imageName;
                $imageUrl = asset($imagePath);
                
                return redirect()->back()->with([
                    'success' => 'Upload thành công!',
                    'image_path' => $imagePath,
                    'image_url' => $imageUrl
                ]);
            } else {
                return redirect()->back()->with('error', 'Không thể lưu file');
            }
        }
        
        return redirect()->back()->with('error', 'Không có file được upload');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
    }
})->name('test.image.upload');

// Debug products images
Route::get('/debug-products', function() {
    $products = \App\Models\Product::take(5)->get(['id', 'name', 'thumbnail_url']);
    $result = [];
    foreach ($products as $product) {
        $result[] = [
            'id' => $product->id,
            'name' => $product->name,
            'thumbnail_url' => $product->thumbnail_url,
            'main_image' => $product->main_image,
            'file_exists' => $product->thumbnail_url ? file_exists(public_path($product->thumbnail_url)) : false
        ];
    }
    return response()->json($result);
});

// Test VNPay expiration logic
Route::get('/test-vnpay-expiration', function() {
    $vnpayOrders = \App\Models\Order::where('payment_method', 'vnpay')
                                   ->where('payment_status', 'unpaid')
                                   ->take(5)
                                   ->get();
    
    $result = [];
    foreach ($vnpayOrders as $order) {
        $result[] = [
            'id' => $order->id,
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'is_expired' => $order->isVNPayExpired(),
            'can_pay' => $order->canPayVNPay(),
            'time_left_seconds' => $order->vnpay_time_left,
            'time_left_minutes' => round($order->vnpay_time_left / 60, 2)
        ];
    }
    
    return response()->json([
        'current_time' => now()->format('Y-m-d H:i:s'),
        'orders' => $result
    ]);
});

// Test Order model
Route::get('/test-order', function() {
    $order = \App\Models\Order::first();
    return response()->json([
        'status' => $order->status,
        'status_label' => $order->status_label,
        'status_color' => $order->status_color
    ]);
});

// Test timezone
Route::get('/test-timezone', function() {
    return response()->json([
        'app_timezone' => config('app.timezone'),
        'php_timezone' => date_default_timezone_get(),
        'current_time' => now()->format('Y-m-d H:i:s T'),
        'sample_order_time' => \App\Models\Order::first()->created_at->format('Y-m-d H:i:s T'),
        'new_timestamp' => now()->toDateTimeString()
    ]);
});

// Test direct VNPay return simulation
Route::get('/direct-vnpay-test', function(\Illuminate\Http\Request $request) {
    // Simulate VNPay return parameters
    $testParams = [
        'vnp_Amount' => '32000000',
        'vnp_BankCode' => 'NCB',
        'vnp_BankTranNo' => 'VNP15202237',
        'vnp_CardType' => 'ATM',
        'vnp_OrderInfo' => 'Thanh toan don hang #19 - MixiShop',
        'vnp_PayDate' => '20251014104337',
        'vnp_ResponseCode' => '00',
        'vnp_TmnCode' => '58X4B4HP',
        'vnp_TransactionNo' => '15202237',
        'vnp_TransactionStatus' => '00',
        'vnp_TxnRef' => '19_1760413284',
        'vnp_SecureHash' => 'd18ca40aafbbca5d07a0d74e79fc4999880c9d1aba60cff77871582344229c87d60bbf88a09ebb025176797b332c4abda0e5814b4ad3f92d4c5d0d3e652a0b3c'
    ];
    
    // Create a new request with test parameters
    $testRequest = new \Illuminate\Http\Request($testParams);
    
    // Call PaymentController vnpayReturn method
    $paymentController = app(\App\Http\Controllers\PaymentController::class);
    return $paymentController->vnpayReturn($testRequest);
});

// Simulate VNPay callback success để test
Route::get('/simulate-vnpay-success/{order}', function(\App\Models\Order $order) {
    // Tìm payment pending của order này
    $payment = \App\Models\Payment::where('order_id', $order->id)
                                  ->where('provider', 'vnpay')
                                  ->where('status', 'pending')
                                  ->latest()
                                  ->first();
    
    if (!$payment) {
        return redirect()->route('checkout.thankyou', ['order' => $order->id])
                       ->with('error', 'Không tìm thấy payment record!');
    }
    
    // Mô phỏng callback thành công
    $mockCallbackData = [
        'vnp_Amount' => ($order->total_amount * 100),
        'vnp_BankCode' => 'NCB',
        'vnp_BankTranNo' => 'VNP' . time(),
        'vnp_CardType' => 'ATM',
        'vnp_OrderInfo' => 'Thanh toan don hang #' . $order->id,
        'vnp_PayDate' => date('YmdHis'),
        'vnp_ResponseCode' => '00', // Success
        'vnp_TmnCode' => config('services.vnpay.tmn_code'),
        'vnp_TransactionNo' => time(),
        'vnp_TxnRef' => $order->id . '_' . time(),
    ];
    
    // Cập nhật payment
    $payment->update([
        'vnp_TransactionNo' => $mockCallbackData['vnp_TransactionNo'],
        'vnp_BankCode' => $mockCallbackData['vnp_BankCode'],
        'vnp_CardType' => $mockCallbackData['vnp_CardType'],
        'vnp_ResponseCode' => $mockCallbackData['vnp_ResponseCode'],
        'vnp_PayDate' => \Carbon\Carbon::createFromFormat('YmdHis', $mockCallbackData['vnp_PayDate']),
        'raw_callback' => json_encode($mockCallbackData),
    ]);
    
    // Mark as paid
    $payment->markAsPaid($mockCallbackData);
    
    return redirect()->route('checkout.thankyou', ['order' => $order->id])
                   ->with('success', 'Thanh toán thành công! (Mô phỏng)');
})->name('simulate.vnpay.success');

// Fallback route để bắt tất cả requests không khớp (đặt cuối cùng)
// TEMPORARILY DISABLED FOR DEBUGGING
/*
Route::fallback(function(\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Log::info('404 Request captured:', [
        'url' => $request->fullUrl(),
        'method' => $request->method(),
        'path' => $request->path(),
        'all_params' => $request->all(),
        'user_agent' => $request->userAgent()
    ]);
    
    // Nếu là VNPay return/callback, xử lý trực tiếp
    if (str_contains($request->path(), 'vnpay') || str_contains($request->path(), 'callback')) {
        $paymentController = app(\App\Http\Controllers\PaymentController::class);
        
        // Kiểm tra có tham số VNPay không
        if ($request->has('vnp_TxnRef')) {
            return $paymentController->vnpayReturn($request);
        }
        
        return $paymentController->vnpayCallback($request);
    }
    
    return response()->view('errors.404', [], 404);
});
*/
