<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait FileUpload
{
    public function save_file($file, $folder): string
    {
        try {
            $fullFolder = config('services.cloudinary.public_folder') . '/' . $folder;
            $uploadOptions = [
                'folder' => $fullFolder,
                'resource_type' => 'auto'       // valid is (image , raw , auto)
            ];

            $uploadFile = cloudinary()->upload($file->getRealPath(), $uploadOptions);
            if (!$uploadFile) {
                return config('services.cloudinary.default_image');
            }

            return $uploadFile->getSecurePath();

        } catch (\Exception $e) {
            return config('services.cloudinary.default_image');
        }

    }

    public function removeFile(string $publicId): void
    {
        cloudinary()->destroy($publicId);
    }

    public function uploadAttachments(String $type,$folder)
    {
        if (request()->has($type)) {
            $file = request()->{$type};

            // Generate a unique name for the file
            // $fileName = time() . '_' . $file->getClientOriginalName();

            // Store the file in the specified folder
            $fileUrl = $file;
            // $fileUrl = $file->storeAs($folder, $fileName, 'public');

            // Return file details
            return [
                'file_url'  => $fileUrl,
                'type'      => $type,
                // 'extension' => $file->getClientOriginalExtension(),
                // 'size'      => $file->getSize(),
                'created_at'=> Carbon::now(),
            ];
        }

        return null;

    }

    public function retrieveFile($name, $folder)
    {
        if (filter_var($name, FILTER_VALIDATE_URL)){
            return $name;
        }
        elseif (isset($value) && $value != 'no-image.png'){
            return asset('/storage/uploads'). '/' . $folder . '/' . $value;
        }
        else{
            return config('services.cloudinary.default_image');
        }
    }

}
