<?php
#P-Zone/Core/Front/Models/ShopBannerStore.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopBannerStore extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['store_id', 'banner_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_banner_store';
    protected $connection = PZ_CONNECTION;
}
