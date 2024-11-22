<?php
$prefixCategory = pz_config('PREFIX_CATEGORY')??'category';
$suffix = pz_config('SUFFIX_URL')??'';
if (file_exists(app_path('Http/Controllers/ShopCategoryController.php'))) {
    $nameSpaceFrontCategory = 'App\Http\Controllers';
} else {
    $nameSpaceFrontCategory = 'PZone\Core\Front\Controllers';
}

Route::group(
    [
        'prefix' => $langUrl.$prefixCategory
    ],
    function ($router) use ($suffix, $nameSpaceFrontCategory) {
        $router->get('/', $nameSpaceFrontCategory.'\ShopCategoryController@allCategoriesProcessFront')
            ->name('category.all');
        $router->get('/{alias}'.$suffix, $nameSpaceFrontCategory.'\ShopCategoryController@categoryDetailProcessFront')
            ->name('category.detail');
    }
);
