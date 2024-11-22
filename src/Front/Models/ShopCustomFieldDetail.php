<?php
#P-Zone/Core/Front/Models/ShopCustomFieldDetail.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class ShopCustomFieldDetail extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    use \PZone\Core\Front\Models\UuidTrait;
    
    public $table          = PZ_DB_PREFIX.'shop_custom_field_detail';
    protected $connection  = PZ_CONNECTION;
    protected $guarded     = [];

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

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = pz_generate_id($type = 'CFD');
            }
        });
    }
}
