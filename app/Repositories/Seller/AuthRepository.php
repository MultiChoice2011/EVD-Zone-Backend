<?php
namespace App\Repositories\Seller;
use App\Enums\GeneralStatusEnum;
use App\Enums\SellerApprovalType;
use App\Helpers\FileUpload;
use App\Models\Permission;
use App\Models\SellerAttachment;
use App\Traits\ApiResponseAble;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\Seller\AuthResource;
use App\Models\Currency;
use App\Models\Seller;

class AuthRepository{
    use FileUpload,ApiResponseAble;
    public function register($request)
    {
        try{
            $permissions = Permission::where('guard_name','sellerApi')->get();
            $createSeller = $this->createSeller($request);
            #assign role super seller for seller user
            $createSeller->assignRole('Super Seller');
            #assign permission to seller user
            $createSeller->givePermissionTo($permissions);
            // Generate a JWT token for the user
            $token = JWTAuth::fromUser($createSeller);
            return $this->ApiSuccessResponseAndToken(AuthResource::make($createSeller),'success message',$token);
        }catch(\Exception $ex){
            return $this->ApiErrorResponse($ex->getMessage(),__('admin.general_error'));
        }
    }
    public function createSeller($data)
    {
        $defaultCurrency = Currency::Active()->where('is_default',1)->first();
        return $this->getModel()::create([
            'name' => $data['name'],
            'owner_name' => $data['owner_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'status' => GeneralStatusEnum::ACTIVE,
            'approval_status' => 'pending',
            'google2fa_secret' => $data['google2fa_secret'],
            'currency_id' => $defaultCurrency->id,
        ]);
    }
    public function updateSeller($seller,$data)
    {
        $seller->name = $data['name'];
        $seller->owner_name = $data['owner_name'];
        $seller->phone = $data['phone'];
        $seller->address_details = $data['address'];
        $seller->commercial_register_number = $data['commercial_register_number'];
        $seller->tax_card_number = $data['tax_card_number'];
        // Handle logo upload
        if (isset($data['logo'])) {
            $imagePath = $data['logo'];
            $seller->logo = $imagePath;  // Store the file path in the database
        }
        $seller->save();

        #Update seller address
        $this->updateSellerAddress($seller, $data);
        if(
            in_array($seller->approval_status, [SellerApprovalType::getTypePending(), SellerApprovalType::getTypeRejected()])
        ){
            #update seller attachments
            $this->updateSellerAttachments($seller,$data);
        }
        return $seller;
    }
    private function updateSellerAddress($seller,$data)
    {
        $addressData = [
            'country_id' => $data['country_id'] ?? null,
            'city_id'    => $data['city_id'] ?? null,
            'region_id'  => $data['region_id'] ?? null,
            'street'    => $data['street'] ?? null,
        ];
        if($seller->sellerAddress)
            $seller->sellerAddress->update($addressData);
        $seller->sellerAddress()->create($addressData);
    }
    private function updateSellerAttachments($seller,$data)
    {
        $attachmentTypes = ['commercial_register','identity','tax_card','more'];
        foreach ($attachmentTypes as $type) {
            $attachmentData = $this->uploadAttachments($type,'uploads/attachments');
            if ($attachmentData) {
                // Update or create attachment
                SellerAttachment::updateOrCreate(
                    ['seller_id' => $seller->id, 'type' => $type,'created_at' => now()],
                    $attachmentData
                );
            }
        }
    }
    private function getModel()
    {
        return '\App\Models\Seller';
    }
    private function getModelById($id)
    {
        return $this->getModel()::find($id);
    }
}
