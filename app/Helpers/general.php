<?php

define('PAGINATION_COUNT_APP', 10);
define('PAGINATION_COUNT_ADMIN', 20);
define('PAGINATION_COUNT_SELLER', 10);
define('PAGINATION_COUNT_WEB', 20);
define('NOTIFY_PAGINATION', 20);
define('RELATED_PRODUCTS_NUMBER', 20);


function generateRandomPassword($length = 8) : string
{
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $specialChars = '!@#';

    // Define character pools
    $pool = $uppercase . $lowercase . $numbers . $specialChars;

    // Initialize password with required characters
    $password = substr(str_shuffle($uppercase), 0, 1) .
        substr(str_shuffle($numbers), 0, 1) .
        substr(str_shuffle($specialChars), 0, 1);

    // Fill the rest of the password with random characters
    $remainingLength = $length - 3;
    $password .= substr(str_shuffle($pool), 0, $remainingLength);

    // Shuffle the password to ensure randomness
    $password = str_shuffle($password);

    return $password;
}
function getActiveGuard()
{
    $guards = array_keys(config('auth.guards'));
    $currentGuard = null;

    foreach ($guards as $guard) {
        if (auth($guard)->check()) {
            $currentGuard = $guard;
            break;
        }
    }

    return $currentGuard;
}
















?>
