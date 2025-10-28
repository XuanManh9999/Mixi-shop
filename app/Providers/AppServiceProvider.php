<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Date;
use App\Models\Coupon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set timezone cho toàn bộ ứng dụng
        date_default_timezone_set(config('app.timezone'));
        
        // Chia sẻ danh sách mã giảm giá cho header
        View::composer('*', function ($view) {
            try {
                $coupons = Coupon::query()
                    ->active()->valid()->available()
                    ->orderByDesc('start_at')
                    ->limit(10)
                    ->get(['id','code','type','value','start_at','end_at','usage_limit','used_count','is_active']);
            } catch (\Throwable $e) {
                $coupons = collect();
            }
            $view->with('headerCoupons', $coupons);
        });
    }
}
