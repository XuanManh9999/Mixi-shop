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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 12, 2);
            $table->decimal('max_discount_amount', 12, 2)->nullable();
            $table->decimal('min_order_amount', 12, 2)->default(0);
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_per_user')->default(1);
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('apply_to_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('apply_to_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->timestamps();
            
            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->index(['start_at', 'end_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
