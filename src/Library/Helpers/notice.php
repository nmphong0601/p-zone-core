<?php
if (!function_exists('pz_notice_add')) {
    /**
     * [pz_notice_add description]
     *
     * @param   string  $type    [$type description]
     * @param   string  $typeId  [$typeId description]
     *
     * @return  [type]           [return description]
     */
    function pz_notice_add(string $type, string $typeId)
    {
        $modelNotice = new PZone\Core\Admin\Models\AdminNotice;
        $content = '';
        $admins = [];
        switch ($type) {
            case 'pz_customer_created':
                $admins = pz_admin_notice_get_admin($type);
                $content = "admin_notice.customer.new";
                break;
            case 'pz_order_created':
                $admins = pz_admin_notice_get_admin($type);
                $content = "admin_notice.order.new";
                break;
            case 'pz_order_success':
                $admins = pz_admin_notice_get_admin($type);
                $content = "admin_notice.order.success";
                break;
            case 'pz_order_update_status':
                $admins = pz_admin_notice_get_admin($type);
                $content = "admin_notice.order.update_status";
                break;
            
            default:
                $admins = pz_admin_notice_get_admin($type);
                $content = $type;
                break;
        }
        if (count($admins)) {
            foreach ($admins as $key => $admin) {
                $modelNotice->create(
                    [
                        'type' => $type,
                        'type_id' => $typeId,
                        'admin_id' => $admin,
                        'content' => $content
                    ]
                );
            }
        }

    }

    /**
     * Get list id admin can get notice
     */
    if (!function_exists('pz_admin_notice_get_admin')) {
        function pz_admin_notice_get_admin(string $type = "")
        {
            if (function_exists('pz_admin_notice_pro_get_admin')) {
                return pz_admin_notice_pro_get_admin($type);
            }

            return (new \PZone\Core\Admin\Models\AdminUser)
            ->selectRaw('distinct '. PZ_DB_PREFIX.'admin_user.id')
            ->join(PZ_DB_PREFIX . 'admin_role_user', PZ_DB_PREFIX . 'admin_role_user.user_id', PZ_DB_PREFIX . 'admin_user.id')
            ->join(PZ_DB_PREFIX . 'admin_role', PZ_DB_PREFIX . 'admin_role.id', PZ_DB_PREFIX . 'admin_role_user.role_id')
            ->whereIn(PZ_DB_PREFIX . 'admin_role.slug', ['administrator','view.all', 'manager'])
            ->pluck('id')
            ->toArray();
        }
    }

}
