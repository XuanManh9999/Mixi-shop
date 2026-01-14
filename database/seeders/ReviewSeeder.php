<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        
        // Lấy users và products
        $users = User::where('is_admin', false)->get();
        $products = Product::all();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Cần có users và products trước khi tạo reviews. Vui lòng chạy DatabaseSeeder trước.');
            return;
        }

        // Lấy hoặc tạo orders đã delivered
        $orders = Order::where('status', 'delivered')->get();
        
        // Nếu chưa có orders delivered, tạo một số orders giả
        if ($orders->isEmpty()) {
            $this->command->info('Đang tạo orders và order_items giả...');
            $orders = $this->createFakeOrders($users, $products, $faker);
        }

        // Tạo reviews
        $this->command->info('Đang tạo reviews...');
        $reviewCount = 0;
        
        foreach ($orders as $order) {
            // Chỉ tạo reviews cho orders đã delivered
            if ($order->status !== 'delivered') {
                continue;
            }

            $orderItems = $order->orderItems;
            
            foreach ($orderItems as $orderItem) {
                // Kiểm tra xem đã có review cho order_item này chưa
                $existingReview = Review::where('order_id', $order->id)
                    ->where('product_id', $orderItem->product_id)
                    ->where('user_id', $order->user_id)
                    ->first();

                if ($existingReview) {
                    continue; // Đã có review rồi, bỏ qua
                }

                // Tạo review với xác suất 70% (không phải tất cả order items đều có review)
                if ($faker->boolean(70)) {
                    $rating = $faker->numberBetween(3, 5); // Phần lớn reviews tích cực (3-5 sao)
                    // Một số ít reviews 1-2 sao
                    if ($faker->boolean(15)) {
                        $rating = $faker->numberBetween(1, 2);
                    }

                    $comment = null;
                    if ($faker->boolean(80)) { // 80% có comment
                        $comments = [
                            'Sản phẩm rất ngon, đóng gói cẩn thận!',
                            'Giao hàng nhanh, chất lượng tốt. Sẽ mua lại!',
                            'Sản phẩm đúng như mô tả, rất hài lòng.',
                            'Tuyệt vời! Đúng vị mình mong đợi.',
                            'Đóng gói đẹp, sản phẩm tươi ngon.',
                            'Giao hàng đúng hẹn, sản phẩm chất lượng.',
                            'Rất hài lòng với chất lượng sản phẩm.',
                            'Sản phẩm ngon nhưng hơi nhỏ so với mong đợi.',
                            'OK, không có gì đặc biệt.',
                            'Sản phẩm ổn, giá cả hợp lý.',
                            'Không như mong đợi, hơi thất vọng.',
                            'Chất lượng kém, không đáng giá tiền.',
                        ];
                        $comment = $faker->randomElement($comments);
                        
                        // Một số reviews có comment dài hơn
                        if ($faker->boolean(30)) {
                            $comment .= ' ' . $faker->sentence(10);
                        }
                    }

                    // Một số reviews có hình ảnh (40%)
                    $images = null;
                    if ($faker->boolean(40)) {
                        $imageCount = $faker->numberBetween(1, 3);
                        $images = [];
                        for ($i = 0; $i < $imageCount; $i++) {
                            // Sử dụng placeholder images từ Unsplash hoặc Picsum
                            $images[] = 'https://picsum.photos/800/800?random=' . $faker->numberBetween(1, 1000);
                        }
                    }

                    // 85% reviews được approved, 15% pending
                    $isApproved = $faker->boolean(85);

                    Review::create([
                        'user_id' => $order->user_id,
                        'product_id' => $orderItem->product_id,
                        'order_id' => $order->id,
                        'order_item_id' => $orderItem->id,
                        'rating' => $rating,
                        'comment' => $comment,
                        'images' => $images,
                        'is_approved' => $isApproved,
                        'created_at' => $faker->dateTimeBetween($order->created_at, 'now'),
                        'updated_at' => now(),
                    ]);

                    $reviewCount++;
                }
            }
        }

        $this->command->info("Đã tạo {$reviewCount} reviews thành công!");
    }

    /**
     * Tạo orders và order_items giả
     */
    private function createFakeOrders($users, $products, $faker)
    {
        $orders = collect();
        $orderCount = $faker->numberBetween(10, 20);

        for ($i = 0; $i < $orderCount; $i++) {
            $user = $users->random();
            $productCount = $faker->numberBetween(1, 3);
            $selectedProducts = $products->random($productCount);

            $subtotal = 0;
            $orderItemsData = [];

            foreach ($selectedProducts as $product) {
                $quantity = $faker->numberBetween(1, 3);
                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $quantity;
                $subtotal += $totalPrice;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                ];
            }

            $shippingFee = $faker->numberBetween(20000, 30000);
            $discountAmount = $faker->boolean(30) ? $faker->numberBetween(10000, 50000) : 0;
            $totalAmount = $subtotal + $shippingFee - $discountAmount;

            // Tạo order với status delivered
            // Chỉ hỗ trợ 2 phương thức: cod (thanh toán khi nhận) và vnpay
            $paymentMethod = $faker->randomElement(['cod', 'vnpay']);
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'delivered',
                'payment_method' => $paymentMethod,
                'payment_status' => 'paid',
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'ship_full_name' => $faker->name,
                'ship_phone' => $faker->phoneNumber,
                'ship_address' => $faker->address,
                'ship_city' => $faker->randomElement(['Hà Nội', 'Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ']),
                'ship_district' => $faker->randomElement(['Quận 1', 'Quận 2', 'Quận 3', 'Quận Hoàn Kiếm', 'Quận Ba Đình', 'Quận Đống Đa']),
                'ship_ward' => $faker->randomElement(['Phường 1', 'Phường 2', 'Phường 3', 'Phường Tràng Tiền', 'Phường Hoàn Kiếm']),
                'note' => $faker->boolean(20) ? $faker->sentence() : null,
                'placed_at' => $placedAt = $faker->dateTimeBetween('-3 months', '-1 week'),
                'confirmed_at' => $faker->dateTimeBetween($placedAt, '-1 week'),
                'created_at' => $placedAt,
                'updated_at' => now(),
            ]);

            // Tạo order items
            foreach ($orderItemsData as $itemData) {
                OrderItem::create(array_merge($itemData, [
                    'order_id' => $order->id,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]));
            }

            $orders->push($order);
        }

        return $orders;
    }
}
