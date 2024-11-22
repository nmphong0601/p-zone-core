<?php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopAttributeGroup extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    public $table = PZ_DB_PREFIX.'shop_attribute_group';
    protected $guarded        = [];
    protected static $getList = null;
    protected $connection = PZ_CONNECTION;

    public static function getListAll()
    {
        if (!self::$getList) {
            self::$getList = self::pluck('name', 'id')->all();
        }
        return self::$getList;
    }

    public function attributeDetails()
    {
        return $this->hasMany(ShopProductAttribute::class, 'attribute_group_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($group) {
            $group->attributeDetails()->delete();
        });
    }
}
