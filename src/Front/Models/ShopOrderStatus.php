<?php
#P-Zone/Core/Front/Models/ShopOrderStatus.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrderStatus extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_order_status';
    protected $connection = PZ_CONNECTION;
    protected $guarded           = [];
    protected static $listStatus = null;

    public static function getIdAll()
    {
        if (!self::$listStatus) {
            self::$listStatus = self::pluck('name', 'id')->all();
        }
        return self::$listStatus;
    }
}
