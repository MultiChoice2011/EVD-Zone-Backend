<?php

namespace App\Services\General\FilesServices;

use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Exception\ApiError;
use Exception;

class CloudinaryService
{
    public function __construct()
    {
    }

    /**
     * @throws ApiError
     */
    public function uploadFile($file, $folder = null): array|bool
    {
        $uploadOptions = [
            'folder' => 'Asus/' . $folder,
            'resource_type' => 'auto'       // valid is (image , raw , auto)
        ];

        $uploadFile = cloudinary()->upload($file->getRealPath(), $uploadOptions);
        if (! $uploadFile) {
            return false;
        }

        return [
            'secure_url' => $uploadFile->getSecurePath(),
            'public_id' => $uploadFile->getPublicId(),
        ];
    }

    public function deleteFile($publicId)
    {
        return cloudinary()->destroy($publicId);
    }


}
