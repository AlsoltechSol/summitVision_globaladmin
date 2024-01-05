<?php

namespace App\Helpers;
use Illuminate\Http\UploadedFile;

class ApiHelpers
{
    public static function processBase64File($input, $file_name, $mime_type)
    {
        if (preg_match('/\/([a-zA-Z0-9+\/=]+);base64,/', $input)) {

            $base64String = substr($input, strpos($input, ',') + 1);
            $decodedData = base64_decode($base64String);

            $tempFilePath = public_path('temp/' . $file_name);
            file_put_contents($tempFilePath, $decodedData);

            $file = new UploadedFile(
                $tempFilePath,
                $file_name,
                $mime_type,
                null,
                true
            );

            return [
                'file' => $file, 'tempFilePath' => $tempFilePath
            ];
        }else{
            return null;
        }
    }
}
