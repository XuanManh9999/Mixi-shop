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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->nullable()->after('user_id');
            $table->string('status')->default('pending')->after('address_id');
            $table->string('payment_method')->after('status');
            $table->string('payment_status')->default('unpaid')->after('payment_method');
            $table->decimal('subtotal_amount', 12, 2)->after('payment_status');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('subtotal_amount');
            $table->decimal('shipping_fee', 12, 2)->default(0)->after('discount_amount');
            $table->decimal('total_amount', 12, 2)->after('shipping_fee');
            $table->string('coupon_code')->nullable()->after('total_amount');
            $table->string('ship_full_name')->after('coupon_code');
            $table->string('ship_phone')->after('ship_full_name');
            $table->text('ship_address')->after('ship_phone');
            $table->string('ship_city')->after('ship_address');
            $table->string('ship_district')->after('ship_city');
            $table->string('ship_ward')->nullable()->after('ship_district');
            $table->text('note')->nullable()->after('ship_ward');
            $table->timestamp('placed_at')->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'user_id', 'address_id', 'status', 'payment_method', 'payment_status',
                'subtotal_amount', 'discount_amount', 'shipping_fee', 'total_amount',
                'coupon_code', 'ship_full_name', 'ship_phone', 'ship_address',
                'ship_city', 'ship_district', 'ship_ward', 'note', 'placed_at'
            ]);
        });
    }
};
