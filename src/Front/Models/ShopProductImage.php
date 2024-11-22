<?php
#P-Zone/Core/Front/Models/ShopProductImage.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductImage extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    use \PZone\Core\Front\Models\UuidTrait;

    public $timestamps = false;
    public $table = PZ_DB_PREFIX.'shop_product_image';
    protected $guarded = [];
    protected $connection = PZ_CONNECTION;

    /*
    Get thumb
     */
    public function getThumb()
    {
        return pz_image_get_path_thumb($this->image);
    }

    /*
    Get image
     */
    public function getImage()
    {
        return pz_image_get_path($this->image);
    }

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
                $model->{$model->getKeyName()} = pz_generate_id($type = 'shop_product_image');
            }
        });
    }
}
