<?php
#PZone\Core\Front\Models\ShopShippingStatus.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopShippingStatus extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_shipping_status';
    protected $guarded           = [];
    protected static $listStatus = null;
    protected $connection = PZ_CONNECTION;
    public static function getIdAll()
    {
        if (!self::$listStatus) {
            self::$listStatus = self::pluck('name', 'id')->all();
        }
        return self::$listStatus;
    }
}
