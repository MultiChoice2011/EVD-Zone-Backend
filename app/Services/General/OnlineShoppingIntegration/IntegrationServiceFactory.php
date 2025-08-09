<?php

namespace App\Services\General\OnlineShoppingIntegration;

use Exception;
use Illuminate\Support\Facades\Log;

class IntegrationServiceFactory
{
    public static function create($integrationConfig) {
        //$integrationConfig = self::keysFormated($integrationConfig);
        switch ($integrationConfig->name) {
            case 'one_card':
                return new OneCardService($integrationConfig);
                break;
            case 'like_card':
                return new LikeCardService($integrationConfig);
                break;
            case 'mintroute_voucher':
                return new MintrouteVoucherService($integrationConfig);
                break;
            case 'mintroute_topup':
                return new MintrouteTopUpService($integrationConfig);
                break;
            case 'sada_live':
                return new SadaLiveService($integrationConfig);
                break;
            case 'hawa_chat':
                return new HawaChatService($integrationConfig);
                break;
            case 'wupex':
                return new WupexService($integrationConfig);
                break;
            default:
                return false;
        }
    }

//    private static function keysFormated($integration)
//    {
//        $formattedKeys = [];
//        foreach ($integration->keys as $key) {
//            $formattedKeys[$key->key] = $key->value;
//        }
//        unset($integration['keys']);
//        $integration['keys'] = $formattedKeys;
//        return $integration;
//    }

}
