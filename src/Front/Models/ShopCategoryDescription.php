<?php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCategoryDescription extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['category_id', 'lang'];
    public $incrementing  = false;
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_category_description';
    protected $connection = PZ_CONNECTION;
    protected $guarded    = [];
}
