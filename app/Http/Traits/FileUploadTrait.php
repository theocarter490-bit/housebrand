<?php

namespace App\Http\Traits;

use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


trait FileUploadTrait
{

    public function movedAsset($folder)
    {
        return 'uploads/' . $folder . '/';
    }
    public function assetUrl($folder, $asset_link)
    {
        return 'uploads/' . $folder . '/' . $asset_link;
    }

    // public function uploadFile($file, $folder)
    // {
    //     $filePath = "";
    //     if ($file) {
    //         if (str_starts_with($file->getMimeType(), 'image/') && $file->getExtension() != 'gif') {

    //             // Create an instance of the image from the file
    //             $img = Image::read($file)->scale(height: 800);
    //             $fileName = time() . '-' . Str::random(8) . '.' . 'webp';
    //             Storage::put($this->assetUrl($folder, $fileName), (string) $img->toWebp(90));
    //         } else {
    //             $fileName = time() . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
    //             Storage::putFileAs($this->movedAsset($folder), $file, $fileName);
    //         }

    //         $filePath = $this->assetUrl($folder, $fileName);
    //         return $filePath;
    //     }
    // }



    public function deleteFile($path)
    {
        if ($path != null) {
            Storage::delete($path);
        }
    }


    public function uploadFile($file, $folder, $type = null, $height = 800)
    {
        $filePath = "";
        $rootDir = 'uploads/';
        $fileSystem = globalSetting('file_system')->value;
        if ($file) {
            if (str_starts_with($file->getMimeType(), 'image/') && $file->getExtension() != 'gif' && $type != 'default') {
                $processedImage = Image::read($file)->scale(height: $height)->toWebp(82);
                $fileName = $rootDir . $folder . '/' . uniqid() . '.webp';
                if ($fileSystem == 1) {
                    Storage::disk('s3')->put($fileName, (string) $processedImage);
                } else {
                    Storage::put($fileName, (string) $processedImage);
                }
            } else {
                $fileName = $rootDir . $folder . '/' . uniqid() . '.' . $file->getClientOriginalExtension();
                if ($fileSystem == 1) {
                    Storage::disk('s3')->putFileAs($file, $fileName);
                } else {
                    Storage::putFileAs($file, $fileName);
                }
            }

            $filePath = $fileName;
        }
        return $filePath;
    }




    private function copyExistingFile($originalPath, $newFolder, $height = 800)
    {
        $fileSystem = globalSetting('file_system')->value; // 1 = S3, else local
        $rootDir = 'uploads/';
        $disk = $fileSystem == 1 ? Storage::disk('s3') : Storage::disk('local');

        if (!$disk->exists($originalPath)) {
            return null;
        }

        // get file content
        $content = $disk->get($originalPath);

        // generate new name
        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        $newFileName = $rootDir . $newFolder . '/' . uniqid() . '.' . $extension;

        // If image (except GIF), convert to WEBP like your uploadFile function
        if (str_starts_with($disk->mimeType($originalPath), 'image/') && $extension !== 'gif') {

            $processedImage = Image::read($content)
                ->scale(height: $height)
                ->toWebp(90);

            $newFileName = $rootDir . $newFolder . '/' . uniqid() . '.webp';
            $disk->put($newFileName, (string) $processedImage);
        } else {
            // Normal file copy
            $disk->put($newFileName, $content);
        }

        return $newFileName;
    }
}
