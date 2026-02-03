<?php

namespace App\Services;

use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CloudinaryProductService
{
    protected $uploadApi;
    protected $folder;

    public function __construct()
    {
        // Cấu hình Cloudinary từ config
        $config = [
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key' => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
                'secure' => config('cloudinary.secure', true)
            ]
        ];

        // Validate config
        if (!config('cloudinary.cloud_name') || !config('cloudinary.api_key') || !config('cloudinary.api_secret')) {
            \Log::error('CloudinaryProductService: Missing Cloudinary configuration', [
                'cloud_name' => config('cloudinary.cloud_name') ? 'SET' : 'MISSING',
                'api_key' => config('cloudinary.api_key') ? 'SET' : 'MISSING',
                'api_secret' => config('cloudinary.api_secret') ? 'SET' : 'MISSING',
            ]);
        }

        $this->uploadApi = new UploadApi($config);
        $this->folder = config('cloudinary.folder', 'mixishop');
    }

    /**
     * Upload product thumbnail to Cloudinary
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $productSlug
     * @return array|null ['url' => string, 'path' => string] (path là public_id)
     */
    public function uploadProductThumbnail($file, $productSlug)
    {
        try {
            // Validate config trước khi upload
            if (!config('cloudinary.cloud_name') || !config('cloudinary.api_key') || !config('cloudinary.api_secret')) {
                \Log::error('CloudinaryProductService: Cannot upload - missing configuration');
                return null;
            }
            
            $filename = $productSlug . '_thumb_' . time();
            $folderPath = $this->folder . '/products/' . $productSlug;
            $publicId = $folderPath . '/' . $filename;
            
            \Log::info('Cloudinary Upload attempt - Thumbnail', [
                'filename' => $filename,
                'public_id' => $publicId,
                'folder' => $this->folder,
                'cloud_name' => config('cloudinary.cloud_name'),
            ]);
            
            // Upload to Cloudinary sử dụng UploadApi instance
            $uploadOptions = [
                'public_id' => $publicId,
                'folder' => $folderPath,
                'resource_type' => 'image',
                'unique_filename' => false,
                'overwrite' => true,
            ];
            
            // Chỉ thêm quality nếu không phải 'auto'
            $quality = config('cloudinary.quality', 'auto');
            if ($quality !== 'auto') {
                $uploadOptions['quality'] = $quality;
            }
            
            // Không thêm format vì Cloudinary tự động detect format từ file
            // format: 'auto' không được hỗ trợ trong upload options
            
            $result = $this->uploadApi->upload($file->getRealPath(), $uploadOptions);
            
            if ($result && isset($result['secure_url'])) {
                $url = $result['secure_url'];
                $publicId = $result['public_id'];
                
                \Log::info('Cloudinary Upload success - Thumbnail', [
                    'url' => $url,
                    'public_id' => $publicId,
                ]);
                
                return [
                    'url' => $url,
                    'path' => $publicId, // Lưu public_id để có thể xóa sau
                ];
            }
            
            \Log::error('Cloudinary upload failed - No secure_url in result', [
                'result' => $result,
            ]);
            return null;
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload thumbnail error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return null;
        }
    }

    /**
     * Upload product gallery image to Cloudinary
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $productSlug
     * @param int $position
     * @return array|null ['url' => string, 'path' => string] (path là public_id)
     */
    public function uploadProductGalleryImage($file, $productSlug, $position)
    {
        try {
            // Validate config trước khi upload
            if (!config('cloudinary.cloud_name') || !config('cloudinary.api_key') || !config('cloudinary.api_secret')) {
                \Log::error('CloudinaryProductService: Cannot upload - missing configuration');
                return null;
            }
            
            $filename = $productSlug . '_gallery_' . $position . '_' . time();
            $folderPath = $this->folder . '/products/' . $productSlug . '/gallery';
            $publicId = $folderPath . '/' . $filename;
            
            \Log::info('Cloudinary Upload attempt - Gallery', [
                'filename' => $filename,
                'public_id' => $publicId,
                'position' => $position,
                'cloud_name' => config('cloudinary.cloud_name'),
            ]);
            
            // Upload to Cloudinary sử dụng UploadApi instance
            $uploadOptions = [
                'public_id' => $publicId,
                'folder' => $folderPath,
                'resource_type' => 'image',
                'unique_filename' => false,
                'overwrite' => true,
            ];
            
            // Chỉ thêm quality nếu không phải 'auto'
            $quality = config('cloudinary.quality', 'auto');
            if ($quality !== 'auto') {
                $uploadOptions['quality'] = $quality;
            }
            
            // Không thêm format vì Cloudinary tự động detect format từ file
            // format: 'auto' không được hỗ trợ trong upload options
            
            $result = $this->uploadApi->upload($file->getRealPath(), $uploadOptions);
            
            if ($result && isset($result['secure_url'])) {
                $url = $result['secure_url'];
                $publicId = $result['public_id'];
                
                \Log::info('Cloudinary Upload success - Gallery', [
                    'url' => $url,
                    'public_id' => $publicId,
                ]);
                
                return [
                    'url' => $url,
                    'path' => $publicId, // Lưu public_id để có thể xóa sau
                ];
            }
            
            \Log::error('Cloudinary upload failed - No secure_url in result', [
                'result' => $result,
            ]);
            return null;
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload gallery error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return null;
        }
    }

    /**
     * Delete file from Cloudinary
     * @param string $publicId
     * @return bool
     */
    public function delete($publicId)
    {
        try {
            if (!$publicId) {
                return false;
            }
            
            // Validate config
            if (!config('cloudinary.cloud_name') || !config('cloudinary.api_key') || !config('cloudinary.api_secret')) {
                \Log::error('CloudinaryProductService: Cannot delete - missing configuration');
                return false;
            }
            
            \Log::info('Cloudinary Delete attempt', [
                'public_id' => $publicId,
            ]);
            
            // Delete from Cloudinary sử dụng UploadApi instance
            $result = $this->uploadApi->destroy($publicId, [
                'resource_type' => 'image'
            ]);
            
            if ($result && isset($result['result']) && $result['result'] === 'ok') {
                \Log::info('Cloudinary Delete success', [
                    'public_id' => $publicId,
                ]);
                return true;
            }
            
            \Log::warning('Cloudinary Delete failed - result not ok', [
                'public_id' => $publicId,
                'result' => $result,
            ]);
            return false;
        } catch (\Exception $e) {
            \Log::error('Cloudinary delete error: ' . $e->getMessage(), [
                'public_id' => $publicId,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return false;
        }
    }
}

