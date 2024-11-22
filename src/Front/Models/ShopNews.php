<?php
#P-Zone/Core/Front/Models/ShopNews.php
namespace PZone\Core\Front\Models;

use PZone\Core\Front\Models\ShopNewsDescription;
use Illuminate\Database\Eloquent\Model;
use PZone\Core\Front\Models\ShopStore;


class ShopNews extends Model
{
    use \PZone\Core\Front\Models\ModelTrait;
    use \PZone\Core\Front\Models\UuidTrait;

    public $table = PZ_DB_PREFIX.'shop_news';
    protected $guarded = [];
    protected $connection = PZ_CONNECTION;

    public function descriptions()
    {
        return $this->hasMany(ShopNewsDescription::class, 'news_id', 'id');
    }

    public function stores()
    {
        return $this->belongsToMany(ShopStore::class, ShopNewsStore::class, 'news_id', 'store_id');
    }
    //Function get text description
    public function getText()
    {
        return $this->descriptions()->where('lang', pz_get_locale())->first();
    }
    public function getTitle()
    {
        return $this->getText()->title ?? '';
    }
    public function getDescription()
    {
        return $this->getText()->description ?? '';
    }
    public function getKeyword()
    {
        return $this->getText()->keyword?? '';
    }
    public function getContent()
    {
        return $this->getText()->content;
    }
    //End  get text description

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
    /**
     * [getUrl description]
     * @return [type] [description]
     */
    public function getUrl($lang = null)
    {
        return pz_route('news.detail', ['alias' => $this->alias, 'lang' => $lang ?? app()->getLocale()]);
    }

    
    public function scopeSort($query, $sortBy = null, $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'sort';
        return $query->orderBy($sortBy, $sortOrder);
    }


    /**
     * Get news detail
     *
     * @param   [string]  $key     [$key description]
     * @param   [string]  $type  [id, alias]
     * @param   [int]  $checkActive
     *
     */
    public function getDetail($key, $type = null, $checkActive = 1)
    {
        if (empty($key)) {
            return null;
        }
        $tableDescription = (new ShopNewsDescription)->getTable();
        $dataSelect = $this->getTable().'.*, '.$tableDescription.'.*';
        $news = $this->selectRaw($dataSelect)
            ->leftJoin($tableDescription, $tableDescription . '.news_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', pz_get_locale());

        $storeId = config('app.storeId');
        if (pz_check_multi_shop_installed()) {
            $tableNewsStore = (new ShopNewsStore)->getTable();
            $tableStore = (new ShopStore)->getTable();
            $news = $news->join($tableNewsStore, $tableNewsStore.'.news_id', $this->getTable() . '.id');
            $news = $news->join($tableStore, $tableStore . '.id', $tableNewsStore.'.store_id');
            $news = $news->where($tableStore . '.status', '1');
            $news = $news->where($tableNewsStore.'.store_id', $storeId);
        }

        if ($type === null) {
            $news = $news->where($this->getTable() .'.id', $key);
        } else {
            $news = $news->where($type, $key);
        }
        if ($checkActive) {
            $news = $news->where($this->getTable() .'.status', 1);
        }
        $news = $news->first();
        return $news;
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($news) {
                $news->descriptions()->delete();
                $news->stores()->detach();

                //Delete custom field
                (new ShopCustomFieldDetail)
                ->join(PZ_DB_PREFIX.'shop_custom_field', PZ_DB_PREFIX.'shop_custom_field.id', PZ_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
                ->where(PZ_DB_PREFIX.'shop_custom_field_detail.rel_id', $news->id)
                ->where(PZ_DB_PREFIX.'shop_custom_field.type', 'shop_news')
                ->delete();

            }
        );

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = pz_generate_id($type = 'shop_news');
            }
        });
    }

    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start()
    {
        return new ShopNews;
    }

    /**
     * build Query
     */
    public function buildQuery()
    {
        $tableDescription = (new ShopNewsDescription)->getTable();

        $dataSelect = $this->getTable().'.*, '.$tableDescription.'.*';
        $query = $this->selectRaw($dataSelect)
            ->leftJoin($tableDescription, $tableDescription . '.news_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', pz_get_locale());
        //search keyword
        if ($this->pz_keyword !='') {
            $query = $query->where(function ($sql) use ($tableDescription) {
                $sql->where($tableDescription . '.title', 'like', '%' . $this->pz_keyword . '%')
                ->orWhere($tableDescription . '.keyword', 'like', '%' . $this->pz_keyword . '%')
                ->orWhere($tableDescription . '.description', 'like', '%' . $this->pz_keyword . '%');
            });
        }
        
        $storeId = config('app.storeId');
        if (pz_check_multi_shop_installed()) {
            $tableNewsStore = (new ShopNewsStore)->getTable();
            $tableStore = (new ShopStore)->getTable();
            $query = $query->join($tableNewsStore, $tableNewsStore.'.news_id', $this->getTable() . '.id');
            $query = $query->join($tableStore, $tableStore . '.id', $tableNewsStore.'.store_id');
            $query = $query->where($tableStore . '.status', '1');
            $query = $query->where($tableNewsStore.'.store_id', $storeId);
        }

        $query = $query->where($this->getTable() .'.status', 1);

        $query = $this->processMoreQuery($query);
        

        if ($this->pz_random) {
            $query = $query->inRandomOrder();
        } else {
            $checkSort = false;
            if (is_array($this->pz_sort) && count($this->pz_sort)) {
                foreach ($this->pz_sort as  $rowSort) {
                    if (is_array($rowSort) && count($rowSort) == 2) {
                        if ($rowSort[0] == 'sort') {
                            $checkSort = true;
                        }
                        $query = $query->sort($rowSort[0], $rowSort[1]);
                    }
                }
            }
        }
        //Use field "sort" if haven't above
        if (empty($checkSort)) {
            $query = $query->orderBy($this->getTable().'.sort', 'asc');
        }
        //Default, will sort id
        $query = $query->orderBy($this->getTable().'.id', 'desc');

        return $query;
    }
}
