<?php

namespace App\Repositories\Seller;

use App\Enums\SellerAttachmentType;
use App\Helpers\FileUpload;
use App\Models\SellerAttachment;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerAttachmentRepository extends BaseRepository
{
    use FileUpload;

    public function store($requestData, $sellerId): bool
    {
        $data = [];
        // make loop in all types of files
        foreach (SellerAttachmentType::getList() as $type){
            $temp = [];
            // check files to handling save it
            if ($requestData->has($type)){
                // make loop in multiple files in one type
                foreach($requestData->{$type} as $key => $file){
                    $temp['seller_id'] = $sellerId;
                    $temp['file_url'] = $file;
                    $temp['type'] = $type;
                    $temp['created_at'] = Carbon::now();
                    $temp['updated_at'] = Carbon::now();
                    // get extension and size of file
                    // $size = $this->convertToHumanReadableSize($file->getSize());
                    // $fileExtension = $file->getClientOriginalExtension();
                    // $temp['extension'] = $fileExtension;
                    // $temp['size'] = $size;
                    $data[] = $temp;
                }
            }
        }
        $this->model->insert($data);
        return true;
    }

    public function deleteBySellerId($sellerId)
    {
        return $this->model->where('seller_id',$sellerId)->delete();
    }

    public function deleteById($id)
    {
        $attachment = $this->model->find($id);
        if (! $attachment)
            return false;
        $attachmentFile = $attachment->file_url;
        $attachment->delete();
        $this->remove_file('sellers', basename($attachmentFile));
        return true;
    }

    private function convertToHumanReadableSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;
        while ($size > 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }
        return round($size, 2) . ' ' . $units[$unitIndex];
    }


    public function model(): string
    {
        return SellerAttachment::class;
    }
}
