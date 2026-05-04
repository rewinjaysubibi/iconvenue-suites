<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Get image URL with fallback - handles missing files gracefully
     */
    public static function getImageUrl(?string $path, ?string $fallback = null): string
    {
        if (!$path) {
            return $fallback ?: asset('images/placeholder.jpg');
        }

        // Already a full URL
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Strip leading slashes or 'storage/' prefix if accidentally stored
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        // Check storage disk first
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        // Check public/storage directly (handles cases where symlink copies exist)
        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }

        return $fallback ?: asset('images/placeholder.jpg');
    }

    /**
     * Get multiple image URLs, filtering out missing ones
     */
    public static function getImageUrls(?array $paths, ?string $fallback = null): array
    {
        if (!$paths || !is_array($paths)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($path) use ($fallback) {
            $url = self::getImageUrl($path, $fallback);
            // Only return if it's not the placeholder (i.e. file actually exists)
            if ($fallback === null && $url === asset('images/placeholder.jpg')) {
                return null;
            }
            return $url;
        }, $paths)));
    }

    /**
     * Get only existing image paths from an array
     */
    public static function getExistingPaths(?array $paths): array
    {
        if (!$paths || !is_array($paths)) {
            return [];
        }

        return array_values(array_filter($paths, function ($path) {
            if (!$path) return false;
            $path = ltrim($path, '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            }
            return Storage::disk('public')->exists($path)
                || file_exists(public_path('storage/' . $path));
        }));
    }

    /**
     * Check if image exists
     */
    public static function imageExists(?string $path): bool
    {
        if (!$path) return false;

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        return Storage::disk('public')->exists($path)
            || file_exists(public_path('storage/' . $path));
    }
}
