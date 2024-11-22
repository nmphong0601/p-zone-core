<?php
namespace PZone\Core\Front\Controllers;

use PZone\Core\Front\Controllers\RootFrontController;
use PZone\Core\Front\Models\ShopProduct;

class ShopProductController extends RootFrontController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Process front all products
     *
     * @param [type] ...$params
     * @return void
     */
    public function allProductsProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            pz_lang_switch($lang);
        }
        return $this->_allProducts();
    }

    /**
     * All products
     * @return [view]
     */
    private function _allProducts()
    {
        $sortBy = 'sort';
        $sortOrder = 'asc';
        $filter_sort = pz_request('filter_sort','','string');
        $filterArr = [
            'price_desc' => ['price', 'desc'],
            'price_asc' => ['price', 'asc'],
            'sort_desc' => ['sort', 'desc'],
            'sort_asc' => ['sort', 'asc'],
            'id_desc' => ['id', 'desc'],
            'id_asc' => ['id', 'asc'],
        ];
        if (array_key_exists($filter_sort, $filterArr)) {
            $sortBy = $filterArr[$filter_sort][0];
            $sortOrder = $filterArr[$filter_sort][1];
        }

        $products = (new ShopProduct)
            ->setLimit(pz_config('product_list'))
            ->setPaginate()
            ->setSort([$sortBy, $sortOrder])
            ->getData();

        pz_check_view($this->templatePath . '.screen.shop_product_list');
        return view(
            $this->templatePath . '.screen.shop_product_list',
            array(
                'title'       => pz_language_render('front.all_product'),
                'keyword'     => '',
                'description' => '',
                'products'    => $products,
                'layout_page' => 'shop_product_list',
                'filter_sort' => $filter_sort,
                'breadcrumbs' => [
                    ['url'    => '', 'title' => pz_language_render('front.all_product')],
                ],
            )
        );
    }

    /**
     * Process front product detail
     *
     * @param [type] ...$params
     * @return void
     */
    public function productDetailProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            $alias = $params[1] ?? '';
            pz_lang_switch($lang);
        } else {
            $alias = $params[0] ?? '';
        }
        return $this->_productDetail($alias);
    }

    /**
     * Get product detail
     *
     * @param   [string]  $alias      [$alias description]
     *
     * @return  [mix]
     */
    private function _productDetail($alias)
    {
        $storeId = config('app.storeId');
        $product = (new ShopProduct)->getDetail($alias, $type = 'alias', $storeId);
        if ($product && $product->status && (!pz_config('product_stock', $storeId) || pz_config('product_display_out_of_stock', $storeId) || $product->stock > 0)) {
            //Update last view
            $product->view += 1;
            $product->date_lastview = pz_time_now();
            $product->save();
            //End last viewed

            //Product last view
            $arrlastView = empty(\Cookie::get('productsLastView')) ? array() : json_decode(\Cookie::get('productsLastView'), true);
            $arrlastView[$product->id] = pz_time_now();
            arsort($arrlastView);
            \Cookie::queue('productsLastView', json_encode($arrlastView), (86400 * config('cart.expire.lastview')));
            //End product last view

            $categories = $product->categories->keyBy('id')->toArray();
            $arrCategoriId = array_keys($categories);

            //first category
            $categoryFirst = $product->categories->first();
            if ($categoryFirst) {
                $dataCategoryFirst = [
                    'url' => $categoryFirst->getUrl(),
                    'title' => $categoryFirst->getTitle(),
                ];
            } else {
                $dataCategoryFirst = [
                    'url' => '',
                    'title' => '',
                ];
            }

            $productRelation = (new ShopProduct)
                ->getProductToCategory($arrCategoriId)
                ->setLimit(pz_config('product_relation', $storeId))
                ->setRandom()
                ->getData();

            pz_check_view($this->templatePath . '.screen.shop_product_detail');
            return view(
                $this->templatePath . '.screen.shop_product_detail',
                array(
                    'title'           => $product->name,
                    'description'     => $product->description,
                    'keyword'         => $product->keyword,
                    'productId'       => $product->id,
                    'product'         => $product,
                    'productRelation' => $productRelation,
                    'og_image'        => pz_file($product->getImage()),
                    'layout_page'     => 'shop_product_detail',
                    'breadcrumbs'     => [
                        ['url'        => pz_route('shop'), 'title' => pz_language_render('front.shop')],
                        $dataCategoryFirst,
                        ['url'        => '', 'title' => $product->name],
                    ],
                )
            );
        } else {
            return $this->itemNotFound();
        }
    }
}
