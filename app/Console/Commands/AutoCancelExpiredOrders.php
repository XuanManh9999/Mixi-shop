<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-cancel {--dry-run : Show what would be cancelled without actually cancelling}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động hủy các đơn hàng hết hạn (pending > 15 phút hoặc confirmed chưa thanh toán > 15 phút)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔍 Đang kiểm tra các đơn hàng hết hạn...');
        
        // Tìm đơn hàng pending hết hạn (> 15 phút chưa xác nhận)
        $pendingExpired = Order::where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes(15))
            ->get();
            
        // Tìm đơn hàng confirmed hết hạn (> 15 phút chưa thanh toán)
        $confirmedExpired = Order::where('status', 'confirmed')
            ->where('payment_status', 'unpaid')
            ->whereNotNull('confirmed_at')
            ->where('confirmed_at', '<=', now()->subMinutes(15))
            ->get();
            
        $totalExpired = $pendingExpired->count() + $confirmedExpired->count();
        
        if ($totalExpired === 0) {
            $this->info('✅ Không có đơn hàng nào cần hủy.');
            return 0;
        }
        
        $this->warn("⚠️  Tìm thấy {$totalExpired} đơn hàng hết hạn:");
        $this->warn("   - {$pendingExpired->count()} đơn pending chưa xác nhận");
        $this->warn("   - {$confirmedExpired->count()} đơn confirmed chưa thanh toán");
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN - Chỉ hiển thị, không thực hiện hủy:');
            
            if ($pendingExpired->count() > 0) {
                $this->info("\n📋 Đơn hàng pending hết hạn:");
                foreach ($pendingExpired as $order) {
                    $minutes = $order->created_at->diffInMinutes(now());
                    $this->line("   - #{$order->id} (Tạo: {$order->created_at->format('d/m/Y H:i')}, {$minutes} phút trước)");
                }
            }
            
            if ($confirmedExpired->count() > 0) {
                $this->info("\n📋 Đơn hàng confirmed hết hạn:");
                foreach ($confirmedExpired as $order) {
                    $minutes = $order->confirmed_at->diffInMinutes(now());
                    $this->line("   - #{$order->id} (Xác nhận: {$order->confirmed_at->format('d/m/Y H:i')}, {$minutes} phút trước)");
                }
            }
            
            return 0;
        }
        
        // Thực hiện hủy đơn hàng
        $cancelled = 0;
        
        // Hủy đơn pending hết hạn
        foreach ($pendingExpired as $order) {
            try {
                $order->autoCancel('Tự động hủy: Quá 15 phút chưa xác nhận');
                $cancelled++;
                $this->info("✅ Đã hủy đơn #{$order->id} (pending hết hạn)");
                
                Log::info("Auto cancelled pending order #{$order->id} - expired after 15 minutes");
            } catch (\Exception $e) {
                $this->error("❌ Lỗi khi hủy đơn #{$order->id}: {$e->getMessage()}");
                Log::error("Failed to auto cancel pending order #{$order->id}: {$e->getMessage()}");
            }
        }
        
        // Hủy đơn confirmed hết hạn
        foreach ($confirmedExpired as $order) {
            try {
                $order->autoCancel('Tự động hủy: Quá 15 phút chưa thanh toán sau khi xác nhận');
                $cancelled++;
                $this->info("✅ Đã hủy đơn #{$order->id} (confirmed hết hạn)");
                
                Log::info("Auto cancelled confirmed order #{$order->id} - expired after 15 minutes without payment");
            } catch (\Exception $e) {
                $this->error("❌ Lỗi khi hủy đơn #{$order->id}: {$e->getMessage()}");
                Log::error("Failed to auto cancel confirmed order #{$order->id}: {$e->getMessage()}");
            }
        }
        
        $this->info("\n🎉 Hoàn thành! Đã hủy {$cancelled}/{$totalExpired} đơn hàng.");
        
        return 0;
    }
}