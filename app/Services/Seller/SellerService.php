<?php
namespace App\Services\Seller;

use Exception;

class SellerService
{

    // get original seller. if he has parent, he is returned
    public static function getOriginalSeller($authSeller)
    {
        if ($authSeller == null) {
            return null;
        }

        return $authSeller->parent_id == null ? $authSeller : $authSeller->parent;
    }



}
