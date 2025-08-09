<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'hyperpay' => [
        'test_mode' => env('HYPERPAY_TEST_MODE') == true ? 'EXTERNAL' : null,
        'access_token' => env('HYPERPAY_TEST_MODE', true) ? env('HYPERPAY_TEST_ACCESS_TOKEN', 'OGFjN2E0Yzk5MGZjNmQwYzAxOTBmZGU1YTU1YzAwYjl8cXREQTR6SGd3Szc2MkoycQ==') : env('HYPERPAY_ACCESS_TOKEN'),
        'entity_id_visa_mastercard' => env('HYPERPAY_TEST_MODE', true) ? env('HYPERPAY_TEST_ENTITY_ID_VISA_MASTERCARD', '8ac7a4c990fc6d0c0190fde7bcfa00bd') : env('HYPERPAY_ENTITY_ID_VISA_MASTERCARD'),
        'entity_id_stcpay' => env('HYPERPAY_TEST_MODE', true) ? env('HYPERPAY_TEST_ENTITY_ID_STCPAY', '8ac7a4c990fc6d0c0190fde7bcfa00bd') : env('HYPERPAY_ENTITY_ID_STCPAY'),
        'entity_id_mada' => env('HYPERPAY_TEST_MODE', true) ? env('HYPERPAY_TEST_ENTITY_ID_MADA', '8ac7a4c990fc6d0c0190fde8ce7e00c1') : env('HYPERPAY_ENTITY_ID_MADA'),
        'currency' => env('HYPERPAY_CURRENCY', 'SAR'),
        'payment_type' => env('HYPERPAY_PAYMENT_TYPE', 'DB'),
        'base_url' => env('HYPERPAY_TEST_MODE', true) ? env('HYPERPAY_TEST_BASE_URL', 'https://eu-test.oppwa.com/') : env('HYPERPAY_BASE_URL'),
    ],

    'laravel_phone' => [
        'countries' => [
            'AF', 'AL', 'DZ', 'AS', 'AD', 'AO', 'AI', 'AQ', 'AG', 'AR', 'AM', 'AW', 'AU', 'AT', 'AZ', 'BS', 'BH', 'BD', 'BB',
            'BY', 'BE', 'BZ', 'BJ', 'BM', 'BT', 'BO', 'BA', 'BW', 'BV', 'BR', 'IO', 'BN', 'BG', 'BF', 'BI', 'KH', 'CM', 'CA',
            'CV', 'KY', 'CF', 'TD', 'CL', 'CN', 'CX', 'CC', 'CO', 'KM', 'CG', 'CD', 'CK', 'CR', 'CI', 'HR', 'CU', 'CY', 'CZ',
            'DK', 'DJ', 'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'EE', 'ET', 'FK', 'FO', 'FJ', 'FI', 'FR', 'GF', 'PF', 'TF',
            'GA', 'GM', 'GE', 'DE', 'GH', 'GI', 'GR', 'GL', 'GD', 'GP', 'GU', 'GT', 'GN', 'GW', 'GY', 'HT', 'HM', 'HN', 'HK',
            'HU', 'IS', 'IN', 'ID', 'IR', 'IQ', 'IE', 'IL', 'IT', 'JM', 'JP', 'JO', 'KZ', 'KE', 'KI', 'KP', 'KR', 'KW', 'KG',
            'LA', 'LV', 'LB', 'LS', 'LR', 'LY', 'LI', 'LT', 'LU', 'MO', 'MG', 'MW', 'MY', 'MV', 'ML', 'MT', 'MH', 'MQ', 'MR',
            'MU', 'YT', 'MX', 'FM', 'MD', 'MC', 'MN', 'ME', 'MS', 'MA', 'MZ', 'MM', 'NA', 'NR', 'NP', 'NL', 'NC', 'NZ', 'NI',
            'NE', 'NG', 'NU', 'NF', 'MK', 'MP', 'NO', 'OM', 'PK', 'PW', 'PS', 'PA', 'PG', 'PY', 'PE', 'PH', 'PN', 'PL', 'PT',
            'PR', 'QA', 'RE', 'RO', 'RU', 'RW', 'SH', 'KN', 'LC', 'PM', 'VC', 'WS', 'SM', 'ST', 'SA', 'SN', 'RS', 'SC', 'SL',
            'SG', 'SK', 'SI', 'SB', 'SO', 'ZA', 'GS', 'ES', 'LK', 'SD', 'SR', 'SJ', 'SZ', 'SE', 'CH', 'SY', 'TJ', 'TZ',
            'TH', 'TL', 'TG', 'TK', 'TO', 'TT', 'TN', 'TR', 'TM', 'TC', 'TV', 'UG', 'UA', 'AE', 'GB', 'US', 'UM', 'UY', 'UZ',
            'VU', 'VE', 'VN', 'VG', 'VI', 'WF', 'EH', 'YE', 'ZM', 'ZW'
        ]
    ],

    'cloudinary' => [
        'public_folder' => env('CLOUDINARY_FOLDER', 'Asus'),
        'default_image' => 'https://res.cloudinary.com/purplecards/image/upload/v1743097784/product-card-placeholder_zlc9zu.png'
    ],

    'whatsapp' => [
        'api_url' => env('WHATSAPP_API_URL'),
        'api_key' => env('WHATSAPP_API_KEY'),
        'messages_create_orders' => env('WHATSAPP_MESSAGES_CREATE_ORDERS'),
        'messages_progress_orders' => env('WHATSAPP_MESSAGES_PROGRESS_ORDERS'),
        'messages_otp' => env('WHATSAPP_MESSAGES_OTP'),
    ],
    'wupex' => [
        'url' => env('WUPEX_API_URL', 'https://sandbox-service.wupex.com'),
        'api_key' => env('WUPEX_API_KEY','N4dSFs07QUI4BBZjb2yetCmusXEJDEPjx3NZ7jnvOBbWYTGozGUIBJGqEEbQdn5l0ST-BJg9XV7IYdgMgKcAigE1yvp00RmEaGo1-bNWX10QyKvAmhHr2gRsNVSe5ez2ixmW6dqrXkw'),
        'merchant_code' => env('WUPEX_MERCHANT_CODE','MerchantCode'),
    ],

    'enc_secret_key' => env('FIREBASE_SERVER_KEY', null),
];
