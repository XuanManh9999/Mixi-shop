<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Console\Command;

class CreateMissingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:create-missing {--dry-run : Show what would be created without actually creating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create missing payment records for orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ordersWithoutPayments = Order::whereDoesntHave('payments')->get();
        
        if ($ordersWithoutPayments->isEmpty()) {
            $this->info('All orders already have payment records.');
            return 0;
        }

        $this->info("Found {$ordersWithoutPayments->count()} orders without payment records.");

        if ($this->option('dry-run')) {
            $this->table(
                ['Order ID', 'Payment Method', 'Amount', 'Status'],
                $ordersWithoutPayments->map(function ($order) {
                    return [
                        $order->id,
                        $order->payment_method,
                        number_format($order->total_amount, 0, ',', '.') . '₫',
                        $order->payment_status
                    ];
                })
            );
            $this->info('This is a dry run. No payments were created.');
            return 0;
        }

        $created = 0;
        foreach ($ordersWithoutPayments as $order) {
            try {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $order->total_amount,
                    'provider' => $order->payment_method === 'vnpay' ? 'vnpay' : 'cod',
                    'status' => $order->payment_status === 'paid' ? 'paid' : 'pending',
                    'paid_at' => $order->payment_status === 'paid' ? $order->updated_at : null,
                ]);
                $created++;
                $this->line("✓ Created payment for Order #{$order->id}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to create payment for Order #{$order->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully created {$created} payment records.");
        return 0;
    }
}