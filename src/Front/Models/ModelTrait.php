<?php

namespace PZone\Core\Front\Models;

/**
 * Trait Model.
 */
trait ModelTrait
{
    protected $pz_limit = 'all'; // all or interger
    protected $pz_paginate = 0; // 0: dont paginate,
    protected $pz_sort = [];
    protected $pz_moreQuery = []; // more query
    protected $pz_random = 0; // 0: no random, 1: random
    protected $pz_keyword = ''; // search search product
 

    
    /**
     * Set value limit
     * @param   [string]  $limit
     */
    public function setLimit($limit)
    {
        if ($limit === 'all') {
            $this->pz_limit = $limit;
        } else {
            $this->pz_limit = (int)$limit;
        }
        return $this;
    }

    /**
     * Set value sort
     * @param   [array]  $sort ['field', 'asc|desc']
     * Support format ['field' => 'asc|desc']
     */
    public function setSort(array $sort)
    {
        if (is_array($sort)) {
            if (count($sort) == 1) {
                foreach ($sort as $kS => $vS) {
                    $sort = [$kS, $vS];
                }
            }
            $this->pz_sort[] = $sort;
        }
        return $this;
    }

    /**
     * [setMoreQuery description]
     *
     * @param  array  $moreQuery  [$moreQuery description]
     * EX: 
     * -- setMoreQuery(['where' => ['columnA','>',12]]) 
     * -- setMoreQuery(['orderBy' => ['columnA','asc']])
     * 
     * @return  [type]              [return description]
     */

    public function setMoreQuery(array $moreQuery)
    {
        if (is_array($moreQuery)) {
            $this->pz_moreQuery[] = $moreQuery;
        }
        return $this;
    }

    /**
     * process more query
     *
     * @param   [type]  $query  [$query description]
     *
     * @return  [type]          [return description]
     */
    protected function processMoreQuery($query) {
        if (count($this->pz_moreQuery)) {
            foreach ($this->pz_moreQuery as $objQuery) {
                if (is_array($objQuery) && count($objQuery) == 1) {
                    foreach ($objQuery as $queryType => $obj) {
                        if (!is_numeric($queryType) && is_array($obj)) {
                            $query = $query->{$queryType}(...$obj);
                        }
                    }
                }
            }
        }
        return $query;
    }

    /**
     * Enable paginate mode
     *  0 - no paginate
     */
    public function setPaginate(int $value = 1)
    {
        $this->pz_paginate = $value;
        return $this;
    }

    /**
     * Set random mode
     */
    public function setRandom(int $value = 1)
    {
        $this->pz_random = $value;
        return $this;
    }
    
    /**
     * Set keyword search
     * @param   [string]  $keyword
     */
    public function setKeyword(string $keyword)
    {
        if (trim($keyword)) {
            $this->pz_keyword = trim($keyword);
        }
        return $this;
    }


    /**
    * Get Sql
    */
    public function getSql()
    {
        $query = $this->buildQuery();
        if (!$this->pz_paginate) {
            if ($this->pz_limit !== 'all') {
                $query = $query->limit($this->pz_limit);
            }
        }
        return $query = $query->toSql();
    }

    /**
    * Get data
    * @param   [array]  $action
    */
    public function getData(array $action = [])
    {
        $query = $this->buildQuery();
        if (!empty($action['query'])) {
            return $query;
        }
        if ($this->pz_paginate) {
            $data =  $query->paginate(($this->pz_limit === 'all') ? 20 : $this->pz_limit);
        } else {
            if ($this->pz_limit !== 'all') {
                $query = $query->limit($this->pz_limit);
            }
            $data = $query->get();
                
            if (!empty($action['keyBy'])) {
                $data = $data->keyBy($action['keyBy']);
            }
            if (!empty($action['groupBy'])) {
                $data = $data->groupBy($action['groupBy']);
            }
        }
        return $data;
    }

    /**
     * Get full data
     *
     * @return  [type]  [return description]
     */
    public function getFull()
    {
        if (method_exists($this, 'getDetail')) {
            return $this->getDetail($this->id);
        } else {
            return $this;
        }
    }
    
    /**
     * Get all custom fields
     *
     * @return void
     */
    public function getCustomFields()
    {
        $typeTmp = explode(PZ_DB_PREFIX, $this->getTable());
        $type = $typeTmp[1] ?? null;
        $data =  (new \PZone\Core\Front\Models\ShopCustomFieldDetail)
            ->join(PZ_DB_PREFIX.'shop_custom_field', PZ_DB_PREFIX.'shop_custom_field.id', PZ_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
            ->select('code', 'name', 'text')
            ->where(PZ_DB_PREFIX.'shop_custom_field_detail.rel_id', $this->id)
            ->where(PZ_DB_PREFIX.'shop_custom_field.type', $type)
            ->where(PZ_DB_PREFIX.'shop_custom_field.status', '1')
            ->get()
            ->keyBy('code');
        return $data;
    }

    /**
     * Get custom field
     *
     * @return void
     */
    public function getCustomField($code = null)
    {
        $typeTmp = explode(PZ_DB_PREFIX, $this->getTable());
        $type = $typeTmp[1] ?? null;
        $data =  (new \PZone\Core\Front\Models\ShopCustomFieldDetail)
            ->join(PZ_DB_PREFIX.'shop_custom_field', PZ_DB_PREFIX.'shop_custom_field.id', PZ_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
            ->select('code', 'name', 'text')
            ->where(PZ_DB_PREFIX.'shop_custom_field_detail.rel_id', $this->id)
            ->where(PZ_DB_PREFIX.'shop_custom_field.type', $type)
            ->where(PZ_DB_PREFIX.'shop_custom_field.status', '1');
        if ($code) {
            $data = $data->where(PZ_DB_PREFIX.'shop_custom_field.code', $code);
        }
        $data = $data->first();
        return $data;
    }

    /*
    Get custom fields via attribute
    $item->custom_fields
     */
    public function getCustomFieldsAttribute()
    {
        return $this->getCustomFields();
    }
}
