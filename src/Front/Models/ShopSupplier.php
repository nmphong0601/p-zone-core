<?php
#P-Zone/Core/Front/Models/ShopSupplier.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;


class ShopSupplier extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    use \PZone\Core\Front\Models\UuidTrait;

    public $table = PZ_DB_PREFIX.'shop_supplier';
    protected $guarded = [];
    private static $getList = null;
    protected $connection = PZ_CONNECTION;

    public static function getListAll()
    {
        if (self::$getList === null) {
            self::$getList = self::get()->keyBy('id');
        }
        return self::$getList;
    }

    /**
     * [getUrl description]
     * @return [type] [description]
     */
    public function getUrl($lang)
    {
        return pz_route('supplier.detail', ['alias' => $this->alias, 'lang' => $lang ?? app()->getLocale()]);
    }

    /*
    *Get thumb
    */
    public function getThumb()
    {
        return pz_image_get_path_thumb($this->image);
    }

    /*
    *Get image
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
     * Get page detail
     *
     * @param   [string]  $key     [$key description]
     * @param   [string]  $type  [id, alias]
     * @param   [int]  $checkActive
     *
     */
    public function getDetail($key, $type = null, $checkActive = 1)
    {
        if (empty($key)) {
            return null;
        }
        if ($type === null) {
            $data = $this->where('id', $key);
        } else {
            $data = $this->where($type, $key);
        }
        if ($checkActive) {
            $data = $data->where('status', 1);
        }
        $data = $data->where('store_id', config('app.storeId'));

        return $data->first();
    }


    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start()
    {
        return new ShopSupplier;
    }


    /**
     * build Query
     */
    public function buildQuery()
    {
        $query = $this->where('status', 1)
        ->where('store_id', config('app.storeId'));

        $query = $this->processMoreQuery($query);
        

        if ($this->pz_random) {
            $query = $query->inRandomOrder();
        } else {
            if (is_array($this->pz_sort) && count($this->pz_sort)) {
                foreach ($this->pz_sort as  $rowSort) {
                    if (is_array($rowSort) && count($rowSort) == 2) {
                        $query = $query->sort($rowSort[0], $rowSort[1]);
                    }
                }
            }
        }

        return $query;
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($supplier) {
                //Delete custom field
                (new ShopCustomFieldDetail)
                ->join(PZ_DB_PREFIX.'shop_custom_field', PZ_DB_PREFIX.'shop_custom_field.id', PZ_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
                ->where(PZ_DB_PREFIX.'shop_custom_field_detail.rel_id', $supplier->id)
                ->where(PZ_DB_PREFIX.'shop_custom_field.type', 'shop_supplier')
                ->delete();
            }
        );
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = pz_generate_id($type = 'shop_supplier');
            }
        });
    }
}
