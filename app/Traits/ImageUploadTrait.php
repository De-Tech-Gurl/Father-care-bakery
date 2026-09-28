<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

trait ImageUploadTrait
{
    protected function uploadImage(UploadedFile $file, string $directory = 'products', ?string $oldPath = null): string
    {
        Storage::disk('public')->makeDirectory($directory);

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $path = $file->store($directory, 'public');

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'image' => 'The image could not be saved. Please try again.',
            ]);
        }

        return $path;
    }
}
