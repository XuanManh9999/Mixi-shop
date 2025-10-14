<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class SimpleCloudinaryService
{
    public function upload(UploadedFile $file, string $folder = null, array $options = []): ?array
    {
        try {
            // Sử dụng Cloudinary trực tiếp với credentials hard-coded
            $cloudinary = cloudinary_url('', [
                'cloud_name' => 'dpbo17rbt',
                'api_key' => '923369223654775',
                'api_secret' => '7szIKlRno-q8XTeuFI2YIeLuZ4',
                'secure' => true
            ]);

            // Upload file
            $uploadResult = \Cloudinary\Uploader::upload($file->getRealPath(), [
                'cloud_name' => 'dpbo17rbt',
                'api_key' => '923369223654775',
                'api_secret' => '7szIKlRno-q8XTeuFI2YIeLuZ4',
                'folder' => $folder ?: 'mixishop',
                'quality' => 'auto',
                'format' => 'auto',
                'resource_type' => 'auto',
                'unique_filename' => true,
                'overwrite' => false
            ]);

            return [
                'public_id' => $uploadResult['public_id'],
                'secure_url' => $uploadResult['secure_url'],
                'url' => $uploadResult['url'] ?? $uploadResult['secure_url'],
                'format' => $uploadResult['format'],
                'width' => $uploadResult['width'],
                'height' => $uploadResult['height'],
                'bytes' => $uploadResult['bytes'],
                'created_at' => $uploadResult['created_at']
            ];

        } catch (\Exception $e) {
            Log::error('Simple Cloudinary upload failed: ' . $e->getMessage());
            return null;
        }
    }

    public function uploadProductImage(UploadedFile $file, string $productSlug, string $type = 'gallery'): ?array
    {
        $folder = 'mixishop/products/' . $productSlug;
        
        $options = [
            'folder' => $folder,
            'public_id' => $productSlug . '_' . $type . '_' . time(),
            'transformation' => [
                'width' => $type === 'thumbnail' ? 400 : 800,
                'height' => $type === 'thumbnail' ? 400 : 800,
                'crop' => 'fill',
                'quality' => 'auto',
                'format' => 'auto'
            ]
        ];

        return $this->upload($file, $folder, $options);
    }

    public function delete(string $publicId): bool
    {
        try {
            $result = \Cloudinary\Uploader::destroy($publicId, [
                'cloud_name' => 'dpbo17rbt',
                'api_key' => '923369223654775',
                'api_secret' => '7szIKlRno-q8XTeuFI2YIeLuZ4'
            ]);
            
            return $result['result'] === 'ok';
        } catch (\Exception $e) {
            Log::error('Simple Cloudinary delete failed: ' . $e->getMessage());
            return false;
        }
    }
}
