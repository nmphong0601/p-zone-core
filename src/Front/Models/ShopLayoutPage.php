<?php
#P-Zone/Core/Front/Models/ShopLayoutPage.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLayoutPage extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;

    public $table = PZ_DB_PREFIX.'shop_layout_page';
    protected $connection = PZ_CONNECTION;

    public static function getPages()
    {
        return self::pluck('name', 'key')->all();
    }

    //Function get text description
    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($obj) {
                //
            }
        );
    }
}
