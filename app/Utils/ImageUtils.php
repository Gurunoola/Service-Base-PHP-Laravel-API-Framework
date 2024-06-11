<?php

namespace App\Utils;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageUtils
{
    public static function optimizeAndStoreImage($base64Image)
    {
        // Check if the provided string is a valid base64-encoded image
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            // Get the image type
            $imageType = strtolower($type[1]);

            // Decode the base64-encoded image
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $imageData = base64_decode($base64Image);

            // Check if the decoding was successful
            if ($imageData === false) {
                throw new \Exception('base64_decode failed');
            }

            // Create the image
            $image = Image::make($imageData);

            // Optimize the image (e.g., resize, encode to webp)
            $image->fit(400, 400, function ($constraint) {
                $constraint->upsize();
            });
            $image->encode('webp', 75);

            // Generate a unique file name
            $path = 'dps/temp/' . uniqid() . '.webp';

            // Save the image temporarily
            Storage::disk('public')->put($path, (string) $image);

            return storage_path('app/public/' . $path);
        } else {
            throw new \Exception('Invalid base64-encoded image string');
        }
    }
}