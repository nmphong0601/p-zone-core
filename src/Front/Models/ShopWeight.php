<?php
#P-Zone/Core/Front/Models/ShopWeight.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopWeight extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_weight';
    protected $connection = PZ_CONNECTION;
    protected $guarded           = [];
    protected static $getList = null;

    public static function getListAll()
    {
        if (!self::$getList) {
            self::$getList = self::pluck('description', 'name')->all();
        }
        return self::$getList;
    }
}
