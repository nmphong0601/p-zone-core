<?php
#P-Zone/Core/Front/Models/ShopCustomerAddress.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCustomerAddress extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    use \PZone\Core\Front\Models\UuidTrait;

    protected $guarded    = [];
    public $table = PZ_DB_PREFIX.'shop_customer_address';
    protected $connection = PZ_CONNECTION;

    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = pz_generate_id($type = 'shop_customer_address');
            }
        });
    }
}
