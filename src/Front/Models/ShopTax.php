<?php
#P-Zone/Core/Front/Models/ShopTax.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopTax extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_tax';
    protected $guarded = [];
    protected $connection = PZ_CONNECTION;

    private static $getList = null;
    private static $status = null;
    private static $arrayId = null;
    private static $arrayValue = null;

    /**
     * Get list item
     *
     * @return  [type]  [return description]
     */
    public static function getListAll()
    {
        if (self::$getList === null) {
            self::$getList = self::get()->keyBy('id');
        }
        return self::$getList;
    }

    /**
     * Get array ID
     *
     * @return  [type]  [return description]
     */
    public static function getArrayId()
    {
        if (self::$arrayId === null) {
            self::$arrayId = self::pluck('id')->all();
        }
        return self::$arrayId;
    }

    /**
     * Get array value
     *
     * @return  [type]  [return description]
     */
    public static function getArrayValue()
    {
        if (self::$arrayValue === null) {
            self::$arrayValue = self::pluck('value', 'id')->all();
        }
        return self::$arrayValue;
    }


    /**
     * Check status tax
     *
     * @return  [type]  [return description]
     */
    public static function checkStatus()
    {
        $arrTaxId = self::getArrayId();
        if (self::$status === null) {
            if (!pz_config('product_tax')) {
                $status = 0;
            } else {
                if (!in_array(pz_config('product_tax'), $arrTaxId)) {
                    $status = 0;
                } else {
                    $status = pz_config('product_tax');
                }
            }
            self::$status = $status;
        }
        return self::$status;
    }
}
