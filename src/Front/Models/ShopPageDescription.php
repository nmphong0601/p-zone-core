<?php
#P-Zone/Core/Front/Models/ShopPageDescription.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopPageDescription extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['lang', 'page_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_page_description';
    protected $connection = PZ_CONNECTION;
}
