<?php
#P-Zone/Core/Front/Models/ShopBannerType.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopBannerType extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_banner_type';
    protected $guarded   = [];
    protected $connection = PZ_CONNECTION;
}
