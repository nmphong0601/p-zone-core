<?php
#P-Zone/Core/Front/Models/ShopProductProperty.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductProperty extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_product_property';
    protected $guarded   = [];
    protected $connection = PZ_CONNECTION;
}
