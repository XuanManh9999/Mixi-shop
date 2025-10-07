<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('provider')->default('vnpay'); // vnpay, momo, cash, bank_transfer
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('VND');
            $table->string('status')->default('pending'); // pending, paid, failed, refunded
            
            // VNPay specific fields
            $table->string('vnp_TransactionNo')->nullable();
            $table->string('vnp_BankCode')->nullable();
            $table->string('vnp_CardType')->nullable();
            $table->string('vnp_ResponseCode')->nullable();
            $table->string('vnp_PayDate')->nullable();
            $table->string('vnp_SecureHash')->nullable();
            
            // Raw callback data
            $table->text('raw_callback')->nullable();
            
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('order_id');
            $table->index('status');
            $table->index('vnp_TransactionNo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
