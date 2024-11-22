<?php
#P-Zone/Core/Front/Models/ShopSubscribe.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSubscribe extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    use \PZone\Core\Front\Models\UuidTrait;

    public $table = PZ_DB_PREFIX.'shop_subscribe';
    protected $guarded      = [];
    protected $connection = PZ_CONNECTION;

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($model) {
            //
            }
        );
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = pz_generate_id($type = 'shop_subscribe');
            }
        });
    }
}
