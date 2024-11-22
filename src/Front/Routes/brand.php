<?php
$prefixBrand = pz_config('PREFIX_BRAND')??'brand';
$suffix = pz_config('SUFFIX_URL')??'';
if (file_exists(app_path('Http/Controllers/ShopBrandController.php'))) {
    $nameSpaceFrontBrand = 'App\Http\Controllers';
} else {
    $nameSpaceFrontBrand = 'PZone\Core\Front\Controllers';
}

Route::group(
    [
        'prefix' => $langUrl.$prefixBrand
    ],
    function ($router) use ($suffix, $nameSpaceFrontBrand) {
        $router->get('/', $nameSpaceFrontBrand.'\ShopBrandController@allBrandsProcessFront')
            ->name('brand.all');
        $router->get('/{alias}'.$suffix, $nameSpaceFrontBrand.'\ShopBrandController@brandDetailProcessFront')
            ->name('brand.detail');
    }
);
