<?php

/**
 * Msegat SMS Service Provider Config File:
 * For More Info Visit: https://msegat.docs.apiary.io/
 */

return [
    /**
     * username for the account in Msegat.com
     */
    "userName" => env("MSEGAT_USERNAME"),

    /**
     * sender name, should be activated from Msegat.com
     */
    "userSender" => env("MSEGAT_USER_SENDER"),

    /**
     * apiKey associated with the account
     */
    "apiKey" => env("MSEGAT_API_KEY"),

    /**
     * Msegat.com Send Message api endpoint.
     */
    "apiUrl" => env("MSEGAT_SEND_MESSAGE_API_ENDPOINT")
];