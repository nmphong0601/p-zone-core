<?php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;
use PZone\Core\Front\Models\ShopStore;


class ShopBanner extends Model
{
    
    use \PZone\Core\Front\Models\ModelTrait;
    use \PZone\Core\Front\Models\UuidTrait;

    public $table = PZ_DB_PREFIX.'shop_banner';
    protected $guarded = [];
    protected $connection = PZ_CONNECTION;

    protected $pz_type = 'all'; // all or interger
    protected $pz_store = 0; // 1: only produc promotion,

    public function stores()
    {
        return $this->belongsToMany(ShopStore::class, ShopBannerStore::class, 'banner_id', 'store_id');
    }

    /*
    Get thumb
    */
    public function getThumb()
    {
        return pz_image_get_path_thumb($this->image);
    }

    /*
    Get image
    */
    public function getImage()
    {
        return pz_image_get_path($this->image);
    }
    
    public function scopeSort($query, $sortBy = null, $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'sort';
        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Get info detail
     *
     * @param   [int]  $id
     * @param   [int]  $checkActive
     *
     */
    public function getDetail($id, $checkActive = 1)
    {
        $storeId = config('app.storeId');
        $dataSelect = $this->getTable().'.*';
        $data =  $this->selectRaw($dataSelect)
            ->where('id', $id);
        if ($checkActive) {
            $data = $data->where($this->getTable() .'.status', 1);
        }
        if (pz_check_multi_shop_installed()) {
            $tableBannerStore = (new ShopBannerStore)->getTable();
            $tableStore = (new ShopStore)->getTable();
            $data = $data->join($tableBannerStore, $tableBannerStore.'.banner_id', $this->getTable() . '.id');
            $data = $data->join($tableStore, $tableStore . '.id', $tableBannerStore.'.store_id');
            $data = $data->where($tableStore . '.status', '1');
            $data = $data->where($tableBannerStore.'.store_id', $storeId);
        }
        $data = $data->first();
        return $data;
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($banner) {
            $banner->stores()->detach();

            //Delete custom field
            (new ShopCustomFieldDetail)
            ->join(PZ_DB_PREFIX.'shop_custom_field', PZ_DB_PREFIX.'shop_custom_field.id', PZ_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
            ->where(PZ_DB_PREFIX.'shop_custom_field_detail.rel_id', $banner->id)
            ->where(PZ_DB_PREFIX.'shop_custom_field.type', 'shop_banner')
            ->delete();

        });


        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = pz_generate_id($type = 'shop_banner');
            }
        });
    }


    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start()
    {
        return new ShopBanner;
    }

    /**
     * Set type
     */
    public function setType($type)
    {
        $this->pz_type = $type;
        return $this;
    }

    /**
     * Get banner
     */
    public function getBanner()
    {
        $this->setType('banner');
        return $this;
    }

    /**
     * Get banner
     */
    public function getBannerStore()
    {
        $this->setType('banner-store');
        return $this;
    }

    /**
     * Get background
     */
    public function getBackground()
    {
        $this->setType('background');
        $this->setLimit(1);
        return $this;
    }

    /**
     * Get background
     */
    public function getBackgroundStore()
    {
        $this->setType('background-store');
        $this->setLimit(1);
        return $this;
    }

    /**
     * Get banner
     */
    public function getBreadcrumb()
    {
        $this->setType('breadcrumb');
        $this->setLimit(1);
        return $this;
    }

    /**
     * Get banner
     */
    public function getBreadcrumbStore()
    {
        $this->setType('breadcrumb-store');
        $this->setLimit(1);
        return $this;
    }

    /**
     * Set store id
     *
     */
    public function setStore($id)
    {
        $this->pz_store = $id;
        return $this;
    }

    /**
     * build Query
     */
    public function buildQuery()
    {
        $dataSelect = $this->getTable().'.*';
        $query =  $this->selectRaw($dataSelect)
            ->where($this->getTable() .'.status', 1);

        $storeId = config('app.storeId');

        if (pz_check_multi_shop_installed()) {
            //Get product active for store
            if (!empty($this->pz_store)) {
                //If sepcify store id
                $storeId = $this->pz_store;
            }
            $tableBannerStore = (new ShopBannerStore)->getTable();
            $tableStore = (new ShopStore)->getTable();
            $query = $query->join($tableBannerStore, $tableBannerStore.'.banner_id', $this->getTable() . '.id');
            $query = $query->join($tableStore, $tableStore . '.id', $tableBannerStore.'.store_id');
            $query = $query->where($tableStore . '.status', '1');
            $query = $query->where($tableBannerStore.'.store_id', $storeId);
        }

        if ($this->pz_type !== 'all') {
            $query = $query->where('type', $this->pz_type);
        }

        $query = $this->processMoreQuery($query);

        if ($this->pz_random) {
            $query = $query->inRandomOrder();
        } else {
            $checkSort = false;
            if (is_array($this->pz_sort) && count($this->pz_sort)) {
                foreach ($this->pz_sort as  $rowSort) {
                    if (is_array($rowSort) && count($rowSort) == 2) {
                        if ($rowSort[0] == 'sort') {
                            $checkSort = true;
                        }
                        $query = $query->sort($rowSort[0], $rowSort[1]);
                    }
                }
            }
            //Use field "sort" if haven't above
            if (empty($checkSort)) {
                $query = $query->orderBy($this->getTable().'.sort', 'asc');
            }
            //Default, will sort id
            $query = $query->orderBy($this->getTable().'.id', 'desc');
        }

        return $query;
    }
}
