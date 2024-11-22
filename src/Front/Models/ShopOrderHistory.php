<?php
#P-Zone/Core/Front/Models/ShopOrderHistory.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrderHistory extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    use \PZone\Core\Front\Models\UuidTrait;

    public $table = PZ_DB_PREFIX.'shop_order_history';
    protected $connection = PZ_CONNECTION;
    const CREATED_AT = 'add_date';
    const UPDATED_AT = null;
    protected $guarded           = [];

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($obj) {
                //
            }
        );

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = pz_generate_id($type = 'shop_order_detail');
            }
        });
    }
}
