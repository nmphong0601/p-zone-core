<?php

namespace PZone\Core\Handlers;

class LfmConfigHandler extends \UniSharp\LaravelFilemanager\Handlers\ConfigHandler
{
    public function userField()
    {
        // If domain is root, dont split folder
        if (session('adminStoreId') == PZ_ID_ROOT) {
            return ;
        }

        if (pz_check_multi_vendor_installed()) {
            return session('adminStoreId');
        } else {
            return;
        }
    }
}
