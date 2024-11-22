<?php
#P-Zone/Core/Front/Models/ShopBrandStore.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopBrandStore extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['store_id', 'brand_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_brand_store';
    protected $connection = PZ_CONNECTION;
}
