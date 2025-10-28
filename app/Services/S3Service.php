<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class S3Service
{
    /**
     * Upload product thumbnail to S3
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $productSlug
     * @return array|null ['url' => string, 'path' => string]
     */
    public function uploadProductThumbnail($file, $productSlug)
    {
        try {
            $filename = $productSlug . '_thumb_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'products/' . $productSlug . '/' . $filename;
            
            \Log::info('S3 Upload attempt - Thumbnail', [
                'filename' => $filename,
                'path' => $path,
                'disk' => 's3',
                'region' => env('AWS_DEFAULT_REGION'),
                'bucket' => env('AWS_BUCKET'),
            ]);
            
            // Upload to S3 (không dùng ACL vì bucket có thể disable ACL)
            // Để public thì cần cấu hình Bucket Policy
            $uploaded = Storage::disk('s3')->put($path, file_get_contents($file));
            
            if ($uploaded) {
                $url = Storage::disk('s3')->url($path);
                \Log::info('S3 Upload success - Thumbnail', [
                    'url' => $url,
                    'path' => $path,
                ]);
                return [
                    'url' => $url,
                    'path' => $path,
                ];
            }
            
            \Log::error('S3 upload failed - Storage::put returned false');
            return null;
        } catch (\Exception $e) {
            \Log::error('S3 upload thumbnail error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Upload product gallery image to S3
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $productSlug
     * @param int $position
     * @return array|null ['url' => string, 'path' => string]
     */
    public function uploadProductGalleryImage($file, $productSlug, $position)
    {
        try {
            $filename = $productSlug . '_gallery_' . $position . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'products/' . $productSlug . '/gallery/' . $filename;
            
            \Log::info('S3 Upload attempt - Gallery', [
                'filename' => $filename,
                'path' => $path,
                'position' => $position,
            ]);
            
            // Upload to S3 (không dùng ACL vì bucket có thể disable ACL)
            $uploaded = Storage::disk('s3')->put($path, file_get_contents($file));
            
            if ($uploaded) {
                $url = Storage::disk('s3')->url($path);
                \Log::info('S3 Upload success - Gallery', [
                    'url' => $url,
                    'path' => $path,
                ]);
                return [
                    'url' => $url,
                    'path' => $path,
                ];
            }
            
            \Log::error('S3 upload failed - Storage::put returned false');
            return null;
        } catch (\Exception $e) {
            \Log::error('S3 upload gallery error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Delete file from S3
     * @param string $path
     * @return bool
     */
    public function delete($path)
    {
        try {
            if ($path && Storage::disk('s3')->exists($path)) {
                return Storage::disk('s3')->delete($path);
            }
            return false;
        } catch (\Exception $e) {
            \Log::error('S3 delete error: ' . $e->getMessage());
            return false;
        }
    }
}

