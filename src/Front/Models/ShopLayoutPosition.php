<?php
#P-Zone/Core/Front/Models/ShopLayoutPosition.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLayoutPosition extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_layout_position';
    protected $connection = PZ_CONNECTION;
    
    public static function getPositions()
    {
        return self::pluck('name', 'key')->all();
    }
}
