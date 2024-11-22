<?php
#P-Zone/Core/Front/Models/ShopProductPromotion.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;
use PZone\Core\Front\Models\ShopProduct;

class ShopProductPromotion extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_product_promotion';
    protected $guarded    = [];
    protected $primaryKey = 'product_id';
    public $incrementing  = false;
    protected $connection = PZ_CONNECTION;

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }
}
