<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PublicUploads
{
    public const DISK = 'uploads';

    public static function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, self::DISK);
    }

    /**
     * Absolute public URL for a stored relative path (e.g. images/category/xxx.jpg).
     * Uses filesystem checks + asset() so it works without storage:link and with subfolders.
     */
    public static function url(?string $relativePath): ?string
    {
        if (! $relativePath) {
            return null;
        }

        $p = trim(str_replace('\\', '/', $relativePath));
        if ($p === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $p)) {
            return $p;
        }

        $p = ltrim($p, '/');

        if (str_starts_with($p, 'uploads/')) {
            $p = substr($p, strlen('uploads/'));
        }

        if (preg_match('#(^|/)images/category/#', $p)) {
            if (! str_starts_with($p, 'images/category/')) {
                $pos = strpos($p, 'images/category/');
                if ($pos !== false) {
                    $p = substr($p, $pos);
                }
            }
        }

        $publicFile = public_path('uploads/'.$p);
        if (is_file($publicFile)) {
            return asset('uploads/'.$p);
        }

        $storageFile = storage_path('app/public/'.$p);
        if (is_file($storageFile)) {
            return asset('storage/'.$p);
        }

        if (Storage::disk(self::DISK)->exists($p)) {
            return asset('uploads/'.$p);
        }

        if (Storage::disk('public')->exists($p)) {
            return asset('storage/'.$p);
        }

        if (str_starts_with($p, 'images/category/')) {
            return asset('uploads/'.$p);
        }

        return null;
    }

    public static function delete(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        $p = trim(str_replace('\\', '/', $relativePath));
        $p = ltrim($p, '/');
        if (str_starts_with($p, 'uploads/')) {
            $p = substr($p, strlen('uploads/'));
        }

        foreach ([self::DISK, 'public'] as $disk) {
            if (Storage::disk($disk)->exists($p)) {
                Storage::disk($disk)->delete($p);
            }
        }
    }
}
