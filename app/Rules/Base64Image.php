<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64Image implements Rule
{
    public function passes($attribute, $value)
    {
        $imageData = explode(',', $value);
        if (count($imageData) < 2) {
            return false;
        }

        $imageFormat = str_replace(['data:image/', ';base64'], '', $imageData[0]);
        $validFormats = ['jpeg', 'png', 'jpg', 'gif', 'svg'];

        if (!in_array($imageFormat, $validFormats)) {
            return false;
        }

        $decodedImage = base64_decode($imageData[1], true);
        if ($decodedImage === false) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'The :attribute field must be a valid base64 image.';
    }
}