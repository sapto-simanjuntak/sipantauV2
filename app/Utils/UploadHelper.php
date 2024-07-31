<?php

namespace App\Utils;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadHelper
{
    public static function upload($file, $path, $name, $generateThumbnail = true, $thumbWidth = 600, $thumbHeight = 600)
    {
        $ext = $file->getClientOriginalExtension();
        $filename = $name . '.' . $ext;
        $relativePath = str_replace('//', '/', $path) . $filename;
        $fullPath = storage_path('app/public/' . $relativePath);

        if (!File::exists(storage_path('app/public/' . $path))) {
            File::makeDirectory(storage_path('app/public/' . $path), 0777, true, true);
        }

        if (Storage::exists('public/' . $relativePath)) {
            Storage::delete('public/' . $relativePath);
        }

        // Check if the file is an image
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
            // Handle image upload
            $imageObject = Image::make($file);
            $imageObject->save($fullPath);
            $filesize = filesize($fullPath);

            $result = [
                'success' => true,
                'filename' => $filename,
                'relative_path' => $relativePath,
                'full_path' => $fullPath,
                'mime_type' => $file->getClientMimeType(),
                'size' => $filesize,
            ];

            if ($generateThumbnail) {
                $filename_thumb = $name . '.thumb.' . $ext;
                $relativePath_thumb = $path . $filename_thumb;
                $fullPath_thumb = storage_path('app/public/' . $relativePath_thumb);

                if (Storage::exists('public/' . $relativePath_thumb)) {
                    Storage::delete('public/' . $relativePath_thumb);
                }

                $imageObject->fit($thumbWidth, $thumbHeight)->save($fullPath_thumb);

                $result['thumb_filename'] = $filename_thumb;
                $result['thumb_relative_path'] = $relativePath_thumb;
                $result['thumb_full_path'] = $fullPath_thumb;

                Storage::put('public/' . $path . $filename, file_get_contents($fullPath), 'public');
                Storage::put('public/' . $path . $filename_thumb, file_get_contents($fullPath_thumb), 'public');
            }
        } else {
            // Handle non-image file upload
            $pathFile = $file->move(storage_path('app/public/' . $path), $filename);

            $result = [
                'success' => true,
                'filename' => $filename,
                'relative_path' => $relativePath,
                'full_path' => $fullPath,
                'mime_type' => $file->getClientMimeType(),
                'size' => $pathFile->getSize(),
            ];
        }

        return $result;
    }
}
