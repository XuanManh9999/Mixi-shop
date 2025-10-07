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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('order_id')->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->after('order_id')->constrained()->onDelete('cascade');
            $table->string('product_name')->after('product_id');
            $table->string('sku')->after('product_name');
            $table->decimal('unit_price', 12, 2)->after('sku');
            $table->integer('quantity')->after('unit_price');
            $table->decimal('total_price', 12, 2)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'product_id', 'product_name', 'sku', 'unit_price', 'quantity', 'total_price']);
        });
    }
};
