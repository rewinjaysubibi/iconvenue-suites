<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait HandlesImageUploads
{
    /**
     * Store an uploaded image with optimized settings
     */
    protected function storeImage(UploadedFile $file, string $directory, array $options = []): string
    {
        $options = array_merge([
            'quality' => 90,
            'max_width' => null,
            'max_height' => null,
            'preserve_original' => false,
        ], $options);

        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;

        if ($options['preserve_original']) {
            // Store original file without processing
            $storedPath = $file->storeAs($directory, $filename, 'public');
            
            // Ensure file is accessible through public symlink
            $this->ensurePublicAccess($storedPath);
            
            return $storedPath;
        }

        // Process image if Intervention Image is available
        if (class_exists('Intervention\Image\Facades\Image')) {
            $image = Image::make($file);

            // Resize if max dimensions are specified
            if ($options['max_width'] || $options['max_height']) {
                $image->resize($options['max_width'], $options['max_height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Save with specified quality
            $fullPath = storage_path('app/public/' . $path);
            $image->save($fullPath, $options['quality']);
            
            // Ensure file is accessible through public symlink
            $this->ensurePublicAccess($path);

            return $path;
        }

        // Fallback to direct storage
        $storedPath = $file->storeAs($directory, $filename, 'public');
        
        // Ensure file is accessible through public symlink
        $this->ensurePublicAccess($storedPath);
        
        return $storedPath;
    }

    /**
     * Store HD image with high quality preservation
     */
    protected function storeHDImage(UploadedFile $file, string $directory): string
    {
        return $this->storeImage($file, $directory, [
            'preserve_original' => true
        ]);
    }

    /**
     * Store optimized image for web display
     */
    protected function storeOptimizedImage(UploadedFile $file, string $directory, int $maxWidth = 1920): string
    {
        return $this->storeImage($file, $directory, [
            'quality' => 85,
            'max_width' => $maxWidth,
            'preserve_original' => false
        ]);
    }

    /**
     * Delete image from storage
     */
    protected function deleteImage(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * Get image URL for display
     */
    protected function getImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return asset('storage/' . $path);
    }

    /**
     * Validate image file
     */
    protected function validateImageFile(UploadedFile $file, array $options = []): array
    {
        $options = array_merge([
            'max_size' => 10240, // 10MB in KB
            'allowed_types' => ['jpeg', 'png', 'jpg', 'gif', 'webp'],
            'min_width' => null,
            'min_height' => null,
        ], $options);

        $errors = [];

        // Check file size
        if ($file->getSize() > $options['max_size'] * 1024) {
            $errors[] = "File size must not exceed " . ($options['max_size'] / 1024) . "MB";
        }

        // Check file type
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $options['allowed_types'])) {
            $errors[] = "File must be one of: " . implode(', ', $options['allowed_types']);
        }

        // Check dimensions if specified
        if ($options['min_width'] || $options['min_height']) {
            $imageSize = getimagesize($file->getPathname());
            if ($imageSize) {
                [$width, $height] = $imageSize;
                
                if ($options['min_width'] && $width < $options['min_width']) {
                    $errors[] = "Image width must be at least {$options['min_width']}px";
                }
                
                if ($options['min_height'] && $height < $options['min_height']) {
                    $errors[] = "Image height must be at least {$options['min_height']}px";
                }
            }
        }

        return $errors;
    }

    /**
     * Ensure file is accessible through public symlink
     */
    protected function ensurePublicAccess(string $storagePath): void
    {
        $sourcePath = storage_path('app/public/' . $storagePath);
        $publicPath = public_path('storage/' . $storagePath);
        
        // Create directory if it doesn't exist
        $publicDir = dirname($publicPath);
        if (!is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        
        // Copy file if it doesn't exist in public or if source is newer
        if (!file_exists($publicPath) || 
            (file_exists($sourcePath) && filemtime($sourcePath) > filemtime($publicPath))) {
            if (file_exists($sourcePath)) {
                copy($sourcePath, $publicPath);
            }
        }
    }
}