<?php
namespace App\Services\Seller;

use App\Helpers\FileUpload;
use App\Http\Resources\Seller\SellerAttachmentResource;
use App\Models\SellerAttachment;
use App\Traits\ApiResponseAble;
use Illuminate\Support\Facades\Storage;

class AttachmentService{
    use ApiResponseAble,FileUpload;
    public function show($id)
    {
        try{
            $attachment = $this->getModelById($id);
            if(!$attachment)
                return $this->ApiErrorResponse([],trans('seller.attachment.not_found'));
            return SellerAttachmentResource::make($attachment);
        }catch(\Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
    public function destroy($id)
    {
        try{
            $attachment = $this->getModelById($id);
            if(!$attachment)
                return $this->ApiErrorResponse([],trans('seller.attachment.not_found'));
            #delete file form database
            $attachmentFile = $attachment->file_url;
            $attachment->delete();
            $this->remove_file('attachments', basename($attachmentFile));
            return $this->ApiSuccessResponse([],'done');
        }catch(\Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }
    public function getModel($id)
    {
        return SellerAttachment::class;
    }
    public function getModelById($id){
        return $this->getModel($id)::find($id);
    }
}
