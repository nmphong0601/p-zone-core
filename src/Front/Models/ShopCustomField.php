<?php
#P-Zone/Core/Front/Models/ShopCustomField.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;
use PZone\Core\Front\Models\ShopCustomFieldDetail;

class ShopCustomField extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    use \PZone\Core\Front\Models\UuidTrait;
    
    public $table          = PZ_DB_PREFIX.'shop_custom_field';
    protected $connection  = PZ_CONNECTION;
    protected $guarded     = [];

    public function details()
    {
        $data  = (new ShopCustomFieldDetail)->where('custom_field_id', $this->id)
            ->get();
        return $data;
    }

    /**
     * Get custom fields
     */
    public function getCustomField($type)
    {
        return $this->where('type', $type)
            ->where('status', 1)
            ->get();
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

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = pz_generate_id($type = 'shop_custom_field');
            }
        });
    }
}
