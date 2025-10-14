<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired {--dry-run : Show what would be cancelled without actually cancelling}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel VNPay orders that have not been paid within 15 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Checking for expired VNPay orders...');
        
        // Tìm các đơn hàng VNPay chưa thanh toán và đã quá 15 phút
        $expiredOrders = Order::where('payment_method', 'vnpay')
                             ->where('payment_status', 'unpaid')
                             ->where('status', '!=', 'cancelled')
                             ->where('created_at', '<=', Carbon::now()->subMinutes(15))
                             ->with(['payments' => function($query) {
                                 $query->where('provider', 'vnpay')->where('status', 'pending');
                             }])
                             ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No expired orders found.');
            return 0;
        }

        $this->info("Found {$expiredOrders->count()} expired orders:");

        $cancelledCount = 0;

        foreach ($expiredOrders as $order) {
            $minutesElapsed = $order->created_at->diffInMinutes(Carbon::now());
            
            $this->line("Order #{$order->id} - Created: {$order->created_at->format('Y-m-d H:i:s')} ({$minutesElapsed} minutes ago)");
            
            if (!$dryRun) {
                try {
                    DB::transaction(function () use ($order) {
                        // Cập nhật order
                        $order->update([
                            'status' => 'cancelled',
                            'payment_status' => 'expired'
                        ]);

                        // Cập nhật payments
                        foreach ($order->payments as $payment) {
                            if ($payment->status === 'pending') {
                                $payment->update([
                                    'status' => 'expired',
                                    'raw_callback' => json_encode([
                                        'reason' => 'Payment timeout after 15 minutes',
                                        'cancelled_at' => Carbon::now()->toISOString(),
                                        'cancelled_by' => 'system'
                                    ])
                                ]);
                            }
                        }
                    });

                    $this->info("  ✓ Cancelled order #{$order->id}");
                    $cancelledCount++;

                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to cancel order #{$order->id}: {$e->getMessage()}");
                }
            } else {
                $this->comment("  → Would cancel order #{$order->id}");
                $cancelledCount++;
            }
        }

        if ($dryRun) {
            $this->info("\nDry run completed. {$cancelledCount} orders would be cancelled.");
            $this->comment('Run without --dry-run to actually cancel the orders.');
        } else {
            $this->info("\nCompleted. {$cancelledCount} orders cancelled successfully.");
        }

        return 0;
    }
}
