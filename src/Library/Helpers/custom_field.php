<?php
use PZone\Core\Front\Models\ShopCustomField;
use PZone\Core\Front\Models\ShopCustomFieldDetail;
use PZone\Core\Admin\Controllers\AdminCustomFieldController;
/**
 * Update custom field
 */
if (!function_exists('pz_update_custom_field') && !in_array('pz_update_custom_field', config('helper_except', []))) {
    function pz_update_custom_field(array $fields = [], string $itemId, string $type)
    {
        $arrFields = array_keys((new AdminCustomFieldController)->fieldTypes());
        if (in_array($type, $arrFields) && !empty($fields)) {
            (new ShopCustomFieldDetail)
                ->join(PZ_DB_PREFIX.'shop_custom_field', PZ_DB_PREFIX.'shop_custom_field.id', PZ_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
                ->where(PZ_DB_PREFIX.'shop_custom_field_detail.rel_id', $itemId)
                ->where(PZ_DB_PREFIX.'shop_custom_field.type', $type)
                ->delete();

            $dataField = [];
            foreach ($fields as $key => $value) {
                $field = (new ShopCustomField)->where('code', $key)->where('type', $type)->first();
                if ($field) {
                    $dataField = pz_clean([
                        'custom_field_id' => $field->id,
                        'rel_id' => $itemId,
                        'text' => is_array($value) ? implode(',', $value) : trim($value),
                    ], [], true);
                    (new ShopCustomFieldDetail)->create($dataField);
                }
            }
        }
    }
}
