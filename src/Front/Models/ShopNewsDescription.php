<?php
#P-Zone/Core/Front/Models/ShopNewsDescription.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopNewsDescription extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['lang', 'news_id'];
    public $incrementing = false;
    protected $guarded = [];
    public $timestamps = false;
    public $table = PZ_DB_PREFIX.'shop_news_description';
    protected $connection = PZ_CONNECTION;
}
