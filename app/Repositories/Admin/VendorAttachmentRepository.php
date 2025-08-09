<?php

namespace App\Repositories\Admin;

use App\Helpers\FileUpload;
use App\Models\VendorAttachment;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;

class VendorAttachmentRepository extends BaseRepository
{
    use FileUpload;

    public function store($requestData, $vendorId): bool
    {
        $data = [];
        if ($requestData->has('attachments')){
            foreach($requestData->attachments as $file){
                $temp['vendor_id'] = $vendorId;
                $temp['file_url'] = $file;
                $temp['created_at'] = Carbon::now();
                $temp['updated_at'] = Carbon::now();
                // $size = $this->convertToHumanReadableSize($file->getSize());
                // $fileExtension = $file->getClientOriginalExtension();
                // $temp['extension'] = $fileExtension;
                // $temp['size'] = $size;
                $data[] = $temp;
            }
            $this->model->insert($data);
        }
        return true;
    }

    public function deleteByVendorId($vendorId)
    {
        return $this->model->where('vendor_id',$vendorId)->delete();
    }

    public function deleteById($id)
    {
        $attachment = $this->model->find($id);
        if (! $attachment)
            return false;
        $attachmentFile = $attachment->file_url;
        $attachment->delete();
        $this->remove_file('vendors', basename($attachmentFile));
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
        return VendorAttachment::class;
    }
}
