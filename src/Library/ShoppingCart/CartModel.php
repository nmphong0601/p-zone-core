<?php
namespace PZone\Core\Library\ShoppingCart;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model
{
    protected $primaryKey = null;
    public $incrementing  = false;
    public $table = PZ_DB_PREFIX.'shop_shoppingcart';
    protected $guarded = [];
    protected $connection = PZ_CONNECTION;
}
