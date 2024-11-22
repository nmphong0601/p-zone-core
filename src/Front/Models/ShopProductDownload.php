<?php
#P-Zone/Core/Front/Models/ShopProductDownload.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;
use PZone\Core\Front\Models\ShopProduct;

class ShopProductDownload extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    use \PZone\Core\Front\Models\UuidTrait;

    protected $primaryKey = ['download_path', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_product_download';
    protected $connection = PZ_CONNECTION;
    
    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
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
                $model->{$model->getKeyName()} = pz_generate_id($type = 'shop_product_download');
            }
        });
    }
}
