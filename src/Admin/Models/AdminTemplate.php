<?php
namespace PZone\Core\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminTemplate extends Model
{
    public $table = PZ_DB_PREFIX.'admin_template';
    protected $guarded = [];
    protected $connection = PZ_CONNECTION;

    /**
     * Get list template installed
     *
     * @return void
     */
    public function getListTemplate()
    {
        return $this->pluck('name', 'key')
            ->all();
    }


    /**
     * Get list template active
     *
     * @return void
     */
    public function getListTemplateActive()
    {
        $arrTemplate =  $this->where('status', 1)
            ->pluck('name', 'key')
            ->all();
        if (!count($arrTemplate)) {
            $arrTemplate['p-zone-light'] = 'P-Zone Light';
        }
        return $arrTemplate;
    }
}
