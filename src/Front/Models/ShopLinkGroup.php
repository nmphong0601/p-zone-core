<?php
#P-Zone/Core/Front/Models/ShopLinkGroup.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLinkGroup extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_link_group';
    protected $guarded   = [];
    protected $connection = PZ_CONNECTION;
}
