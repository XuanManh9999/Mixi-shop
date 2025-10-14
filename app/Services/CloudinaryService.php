<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary;
    protected $uploadApi;
    protected $adminApi;

    public function __construct()
    {
        // Hard-coded config for testing
        $config = [
            'cloud' => [
                'cloud_name' => 'dpbo17rbt',
                'api_key' => '923369223654775',
                'api_secret' => '7szIKlRno-q8XTeuFI2YIeLuZ4',
                'secure' => true
            ]
        ];

        $this->cloudinary = new Cloudinary($config);
        $this->uploadApi = new UploadApi($config);
        $this->adminApi = new AdminApi($config);
    }

    /**
     * Upload file to Cloudinary
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array|null
     */
    public function upload(UploadedFile $file, string $folder = null, array $options = []): ?array
    {
        try {
            $folder = $folder ?: 'mixishop';
            
            $defaultOptions = [
                'folder' => $folder,
                'quality' => 'auto',
                'format' => 'auto',
                'resource_type' => 'auto',
                'unique_filename' => true,
                'overwrite' => false
            ];

            $uploadOptions = array_merge($defaultOptions, $options);

            $result = $this->uploadApi->upload($file->getRealPath(), $uploadOptions);

            return [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'url' => $result['url'],
                'format' => $result['format'],
                'width' => $result['width'],
                'height' => $result['height'],
                'bytes' => $result['bytes'],
                'created_at' => $result['created_at']
            ];

        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Upload product image with transformations
     *
     * @param UploadedFile $file
     * @param string $productSlug
     * @param string $type (thumbnail, gallery)
     * @return array|null
     */
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

        return $this->upload($file, null, $options);
    }

    /**
     * Delete file from Cloudinary
     *
     * @param string $publicId
     * @return bool
     */
    public function delete(string $publicId): bool
    {
        try {
            $result = $this->uploadApi->destroy($publicId);
            return $result['result'] === 'ok';
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate URL with transformations
     *
     * @param string $publicId
     * @param array $transformations
     * @return string
     */
    public function url(string $publicId, array $transformations = []): string
    {
        return $this->cloudinary->image($publicId)
                                ->addTransformation($transformations)
                                ->toUrl();
    }

    /**
     * Generate secure URL with transformations
     *
     * @param string $publicId
     * @param array $transformations
     * @return string
     */
    public function secureUrl(string $publicId, array $transformations = []): string
    {
        return $this->cloudinary->image($publicId)
                                ->addTransformation($transformations)
                                ->secure()
                                ->toUrl();
    }

    /**
     * Get optimized URL for different sizes
     *
     * @param string $publicId
     * @param string $size (thumbnail, medium, large)
     * @return string
     */
    public function getOptimizedUrl(string $publicId, string $size = 'medium'): string
    {
        $transformations = config("cloudinary.transformations.{$size}", [
            'width' => 600,
            'height' => 600,
            'crop' => 'fill',
            'quality' => 'auto',
            'format' => 'auto'
        ]);

        return $this->secureUrl($publicId, $transformations);
    }

    /**
     * Get image info
     *
     * @param string $publicId
     * @return array|null
     */
    public function getImageInfo(string $publicId): ?array
    {
        try {
            return $this->adminApi->asset($publicId);
        } catch (\Exception $e) {
            Log::error('Cloudinary get image info failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract public_id from Cloudinary URL
     *
     * @param string $url
     * @return string|null
     */
    public function extractPublicId(string $url): ?string
    {
        // Pattern for Cloudinary URLs
        $pattern = '/\/v\d+\/(.+)\.[a-zA-Z]{3,4}$/';
        
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
