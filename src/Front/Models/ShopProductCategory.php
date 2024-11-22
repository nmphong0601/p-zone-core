<?php
#P-Zone/Core/Front/Models/ShopProductCategory.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductCategory extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['category_id', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_product_category';
    protected $connection = PZ_CONNECTION;
}
