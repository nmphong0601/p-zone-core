<?php
#P-Zone/Core/Front/Models/ShopStoreDescription.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopStoreDescription extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['lang', 'store_id'];
    public $incrementing = false;
    protected $guarded = [];
    public $timestamps = false;
    public $table = PZ_DB_PREFIX.'admin_store_description';
    protected $connection = PZ_CONNECTION;
}
