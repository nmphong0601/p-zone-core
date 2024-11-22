<?php

//Product kind
define('PZ_PRODUCT_SINGLE', 0);
define('PZ_PRODUCT_BUILD', 1);
define('PZ_PRODUCT_GROUP', 2);
//Product property
define('PZ_PROPERTY_PHYSICAL', 'physical');
define('PZ_PROPERTY_DOWNLOAD', 'download');
// list ID admin guard
define('PZ_GUARD_ADMIN', ['1']); // admin
// list ID language guard
define('PZ_GUARD_LANGUAGE', ['1', '2']); // vi, en
// list ID currency guard
define('PZ_GUARD_CURRENCY', ['1', '2']); // vndong , usd
// list ID ROLES guard
define('PZ_GUARD_ROLES', ['1', '2']); // admin, only view

/**
 * Admin define
 */
define('PZ_ADMIN_MIDDLEWARE', ['web', 'admin']);
define('PZ_FRONT_MIDDLEWARE', ['web', 'front']);
define('PZ_API_MIDDLEWARE', ['api', 'api.extend']);
define('PZ_CONNECTION', 'mysql');
define('PZ_CONNECTION_LOG', 'mysql');
//Prefix url admin
define('PZ_ADMIN_PREFIX', config('const.ADMIN_PREFIX'));
//Prefix database
define('PZ_DB_PREFIX', config('const.DB_PREFIX'));
// Root ID store
define('PZ_ID_ROOT', 1);
define('PZ_ID_GLOBAL', 0);
