<?php
#P-Zone/Core/Front/Models/ShopPaymentStatus.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopPaymentStatus extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_payment_status';
    protected $guarded   = [];
    protected $connection = PZ_CONNECTION;
    protected static $listStatus = null;
    public static function getIdAll()
    {
        if (!self::$listStatus) {
            self::$listStatus = self::pluck('name', 'id')->all();
        }
        return self::$listStatus;
    }
}
