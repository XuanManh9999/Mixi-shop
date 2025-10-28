<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckS3Config extends Command
{
    protected $signature = 's3:check-config';
    protected $description = 'Kiểm tra nhanh cấu hình S3 trong .env';

    public function handle()
    {
        $this->info('🔍 Kiểm tra cấu hình S3 trong .env');
        $this->newLine();

        $configs = [
            'FILESYSTEM_DISK' => env('FILESYSTEM_DISK'),
            'AWS_ACCESS_KEY_ID' => env('AWS_ACCESS_KEY_ID'),
            'AWS_SECRET_ACCESS_KEY' => env('AWS_SECRET_ACCESS_KEY'),
            'AWS_DEFAULT_REGION' => env('AWS_DEFAULT_REGION'),
            'AWS_BUCKET' => env('AWS_BUCKET'),
            'AWS_URL' => env('AWS_URL'),
        ];

        $hasError = false;

        foreach ($configs as $key => $value) {
            $status = $value ? '✅' : '❌';
            $displayValue = $value ?? '(chưa cấu hình)';
            
            // Mask sensitive values
            if (in_array($key, ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY']) && $value) {
                $displayValue = substr($value, 0, 4) . '...' . substr($value, -4);
            }
            
            $this->line("$status $key: $displayValue");
            
            if (!$value && in_array($key, ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION', 'AWS_BUCKET'])) {
                $hasError = true;
            }
        }

        $this->newLine();

        if ($hasError) {
            $this->error('❌ Thiếu cấu hình quan trọng!');
            $this->newLine();
            $this->info('📝 Cần thêm vào file .env:');
            $this->line('');
            $this->line('FILESYSTEM_DISK=s3');
            $this->line('AWS_ACCESS_KEY_ID=your-access-key-id');
            $this->line('AWS_SECRET_ACCESS_KEY=your-secret-access-key');
            $this->line('AWS_DEFAULT_REGION=ap-southeast-1');
            $this->line('AWS_BUCKET=your-bucket-name');
            $this->line('AWS_URL=https://your-bucket-name.s3.ap-southeast-1.amazonaws.com');
            $this->newLine();
            $this->info('📖 Xem chi tiết: HUONG_DAN_CAU_HINH_S3.md');
            return 1;
        }

        $this->info('✅ Cấu hình S3 đã đầy đủ!');
        $this->info('💡 Chạy: php artisan s3:test để test kết nối');
        return 0;
    }
}

