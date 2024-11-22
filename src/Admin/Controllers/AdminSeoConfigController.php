<?php
namespace PZone\Core\Admin\Controllers;

use PZone\Core\Admin\Controllers\RootAdminController;

class AdminSeoConfigController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $data = [
            'title'    => pz_language_render('admin.seo.config'),
            'subTitle' => '',
            'icon'     => 'fa fa-indent',
        ];
        $data['urlUpdateConfigGlobal'] = pz_route_admin('admin_config_global.update');
        return view($this->templatePathAdmin.'screen.seo_config')
            ->with($data);
    }
}
