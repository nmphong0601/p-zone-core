<?php
#P-Zone/Core/Front/Models/ShopProductAttribute.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductAttribute extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_product_attribute';
    protected $guarded = [];
    protected $connection = PZ_CONNECTION;
    public function attGroup()
    {
        return $this->belongsTo(ShopAttributeGroup::class, 'attribute_group_id', 'id');
    }
}
