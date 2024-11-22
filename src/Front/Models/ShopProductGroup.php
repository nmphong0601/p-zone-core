<?php
#P-Zone/Core/Front/Models/ShopProductGroup.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;
use PZone\Core\Front\Models\ShopProduct;

class ShopProductGroup extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['group_id', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_product_group';
    protected $connection = PZ_CONNECTION;

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }
}
