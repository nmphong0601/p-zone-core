<?php
/**
 * Route front
 */
if(pz_config_exist('Plugin_Key')) {
Route::group(
    [
        'prefix'    => 'plugin/PluginUrlKey',
        'namespace' => 'App\Plugins\Plugin_Code\Plugin_Key\Controllers',
    ],
    function () {
        Route::get('index', 'FrontController@index')
        ->name('PluginUrlKey.index');
    }
);
}
/**
 * Route admin
 */
if(pz_config_exist('Plugin_Key', PZ_ID_ROOT)) {
Route::group(
    [
        'prefix' => PZ_ADMIN_PREFIX.'/PluginUrlKey',
        'middleware' => PZ_ADMIN_MIDDLEWARE,
        'namespace' => 'App\Plugins\Plugin_Code\Plugin_Key\Admin',
    ], 
    function () {
        Route::get('/', 'AdminController@index')
        ->name('admin_PluginUrlKey.index');
    }
);
}