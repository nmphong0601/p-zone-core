<?php
#P-Zone/Core/Front/Models/ShopCustomerPasswordReset.php
namespace PZone\Core\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCustomerPasswordReset extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['token'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = PZ_DB_PREFIX.'shop_password_resets';
    protected $connection = PZ_CONNECTION;
}
