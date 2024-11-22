<?php
#P-Zone/Core/Front/Models/ShopLength.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLength extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_length';
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
