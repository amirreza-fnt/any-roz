<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default marketer ID (until marketer auth is implemented)
    |--------------------------------------------------------------------------
    */
    'default_marketer_id' => (int) env('MARKETING_DEFAULT_MARKETER_ID', 1),

    /*
    |--------------------------------------------------------------------------
    | User ID stored on orders generated from approved marketing sales
    |--------------------------------------------------------------------------
    |
    | Orders require a users row for the foreign key. Until storefront users
    | are linked to marketing sales, this ID is used (defaults to first user).
    |
    */
    'order_owner_user_id' => (int) env('MARKETING_ORDER_OWNER_USER_ID', 1),
];
