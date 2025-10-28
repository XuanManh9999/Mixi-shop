<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestS3Connection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test AWS S3 connection and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Đang kiểm tra cấu hình S3...');
        $this->newLine();

        // Check S3 configuration
        $this->info('📋 Cấu hình hiện tại:');
        $this->line('  - AWS Access Key ID: ' . (env('AWS_ACCESS_KEY_ID') ? '✅ Đã cấu hình' : '❌ Chưa cấu hình'));
        $this->line('  - AWS Secret Key: ' . (env('AWS_SECRET_ACCESS_KEY') ? '✅ Đã cấu hình' : '❌ Chưa cấu hình'));
        $this->line('  - AWS Region: ' . (env('AWS_DEFAULT_REGION', 'Chưa cấu hình')));
        $this->line('  - AWS Bucket: ' . (env('AWS_BUCKET', 'Chưa cấu hình')));
        $this->line('  - Filesystem Disk: ' . config('filesystems.default'));
        $this->newLine();

        if (!env('AWS_ACCESS_KEY_ID') || !env('AWS_SECRET_ACCESS_KEY') || !env('AWS_BUCKET')) {
            $this->error('❌ Vui lòng cấu hình đầy đủ thông tin S3 trong file .env');
            $this->info('📖 Xem hướng dẫn chi tiết tại: HUONG_DAN_CAU_HINH_S3.md');
            return 1;
        }

        // Test connection
        $this->info('🔌 Đang test kết nối đến S3...');
        
        try {
            $testContent = 'MixiShop S3 Test - ' . now()->toDateTimeString();
            $testPath = 'test/connection-test-' . time() . '.txt';
            
            // Test upload
            $this->info('📤 Đang upload file test...');
            
            // Debug: Check if S3 client is initialized
            try {
                $disk = Storage::disk('s3');
                $this->line('  ✓ S3 disk initialized');
            } catch (\Exception $e) {
                $this->error('  ✗ Cannot initialize S3 disk: ' . $e->getMessage());
                throw $e;
            }
            
            $uploaded = $disk->put($testPath, $testContent, 'public');
            $this->line('  Upload result: ' . ($uploaded ? 'true' : 'false'));
            
            if ($uploaded) {
                $this->info('✅ Upload thành công!');
                
                // Get URL
                $url = Storage::disk('s3')->url($testPath);
                $this->line('  📍 URL: ' . $url);
                
                // Test read
                $this->info('📥 Đang đọc file từ S3...');
                $content = Storage::disk('s3')->get($testPath);
                
                if ($content === $testContent) {
                    $this->info('✅ Đọc file thành công!');
                } else {
                    $this->error('❌ Nội dung file không khớp!');
                }
                
                // Test delete
                $this->info('🗑️  Đang xóa file test...');
                $deleted = Storage::disk('s3')->delete($testPath);
                
                if ($deleted) {
                    $this->info('✅ Xóa file thành công!');
                } else {
                    $this->warn('⚠️  Không thể xóa file test');
                }
                
                $this->newLine();
                $this->info('🎉 Kết nối S3 hoạt động hoàn hảo!');
                $this->info('✨ Bạn có thể bắt đầu upload ảnh sản phẩm.');
                
                return 0;
                
            } else {
                $this->error('❌ Không thể upload file test!');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Lỗi kết nối S3:');
            $this->line('  Message: ' . $e->getMessage());
            $this->line('  File: ' . $e->getFile() . ':' . $e->getLine());
            $this->newLine();
            
            // Check for previous exception (AWS SDK exception)
            if ($previous = $e->getPrevious()) {
                $this->line('🔍 Chi tiết lỗi AWS:');
                $this->line('  Previous Message: ' . $previous->getMessage());
                
                if (method_exists($previous, 'getAwsErrorCode')) {
                    $this->line('  AWS Error Code: ' . $previous->getAwsErrorCode());
                }
                if (method_exists($previous, 'getStatusCode')) {
                    $this->line('  HTTP Status: ' . $previous->getStatusCode());
                }
                if (method_exists($previous, 'getAwsRequestId')) {
                    $this->line('  Request ID: ' . $previous->getAwsRequestId());
                }
                $this->newLine();
            }
            
            // Show more details if available
            if (method_exists($e, 'getAwsErrorCode')) {
                $this->line('  AWS Error Code: ' . $e->getAwsErrorCode());
            }
            if (method_exists($e, 'getStatusCode')) {
                $this->line('  HTTP Status: ' . $e->getStatusCode());
            }
            
            $this->newLine();
            $this->info('💡 Các lỗi thường gặp:');
            $this->line('  - InvalidAccessKeyId: Access Key sai');
            $this->line('  - SignatureDoesNotMatch: Secret Key sai');
            $this->line('  - NoSuchBucket: Bucket không tồn tại hoặc region sai');
            $this->line('  - AccessDenied: IAM user chưa có quyền');
            $this->line('  - PermanentRedirect: Region sai (bucket ở region khác)');
            $this->newLine();
            $this->info('📖 Xem hướng dẫn chi tiết tại: HUONG_DAN_CAU_HINH_S3.md');
            
            return 1;
        }
    }
}
