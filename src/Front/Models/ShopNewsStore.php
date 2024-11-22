<?php
#P-Zone/Core/Front/Models/ShopNewsStore.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopNewsStore extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['store_id', 'news_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_news_store';
    protected $connection = PZ_CONNECTION;
}
