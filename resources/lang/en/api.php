<?php

use App\Models\Product;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    "auth" =>[
        'account_created' => 'Your account has been created successfully, and an activation code has been sent to your email.',
        'logged_in' => 'Logged in successfully.',
        "account_not_found" => 'Account not found.',
        "account_found" => 'Account found.',
        'wrong_data' => 'Invalid credentials.',
        'pasword_changed' => 'Password changed successfully.',
        "logged_out_successfully" => 'Logged out successfully.',
        "inactive_client"   => "This account is inactive.",
        "inactive_email"  => "Email is not verified.",
        "email_verified"    => 'Email has been verified.',
        "verification_main_sent" => "The account activation code has been sent to your email.",
        "wrong_verification_code" => "Invalid activation code.",
    ],

    "product" =>[
        'added_to_favorite' => 'The product has been added to your favorites.',
        'removed_from_favorite' => 'The product has been removed from your favorites.',
        "review_added"    =>"Review added successfully."
    ],

    'ItemCreatedSuccessfully' => 'Item created successfully.',
    'ItemUpdatedSuccessfully' => 'Item updated successfully.',
    'ItemDeletedSuccessfully' => 'Item deleted successfully.',
    'unAuthorized' => 'Unauthorized.',
    'unAuthenticated' => 'Unauthenticated.',
    'Ok' => 'Success',
    'ItemNotFound' => 'This item was not found.',
    'UnprocessableEntity' => 'Validation error.',
    'message_data' => 'All items.',
    'something_error' => 'Something went wrong. Please try again.',

];
