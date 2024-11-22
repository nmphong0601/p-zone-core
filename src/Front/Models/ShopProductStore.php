<?php
#P-Zone/Core/Front/Models/ShopProductStore.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductStore extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['store_id', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_product_store';
    protected $connection = PZ_CONNECTION;
}
