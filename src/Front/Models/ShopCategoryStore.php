<?php
#P-Zone/Core/Front/Models/ShopCategoryStore.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCategoryStore extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['store_id', 'category_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_category_store';
    protected $connection = PZ_CONNECTION;
}
