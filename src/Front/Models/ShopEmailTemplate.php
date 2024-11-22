<?php
#P-Zone/Core/Front/Models/ShopEmailTemplate.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopEmailTemplate extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    use \PZone\Core\Front\Models\UuidTrait;
    
    public $table = PZ_DB_PREFIX.'shop_email_template';
    protected $guarded = [];
    protected $connection = PZ_CONNECTION;

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
                $model->{$model->getKeyName()} = pz_generate_id($type = 'shop_email_template');
            }
        });
    }
}
