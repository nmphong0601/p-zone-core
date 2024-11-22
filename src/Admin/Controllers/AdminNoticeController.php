<?php
namespace PZone\Core\Admin\Controllers;

use PZone\Core\Admin\Controllers\RootAdminController;
use Validator;
use PZone\Core\Admin\Models\AdminNotice;
class AdminNoticeController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = [
            'title'         => pz_language_render('admin_notice.title'),
            'subTitle'      => '',
            'icon'          => 'fa fa-indent',
            'urlDeleteItem' => '',
            'removeList'    => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'css'           => '',
            'js'            => '',
        ];
        //Process add content
        $data['menuRight']    = pz_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft']     = pz_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = pz_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft']  = pz_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom']  = pz_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'type'    => 'Type',
            'type_id' => 'Type ID',
            'content' => 'Content',
            'date'    => pz_language_render('admin.created_at')
        ];
        $dataTmp = (new AdminNotice)->getNoticeListAdmin();

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataMap = [
                'type' => '<a class="notice-'.(($row->status) ? 'read':'unread').'" href="'.pz_route_admin('admin_notice.url',['type' => $row->type,'typeId' => $row->type_id]).'">'.$row->type.'</a>',
                'type_id' => $row->type_id,
                'content' => pz_language_render($row->content),
                'date' => $row->created_at,
            ];
            $dataTr[$row['id']] = $dataMap;
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = pz_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        return view($this->templatePathAdmin.'screen.list')
            ->with($data);
    }

    /**
     * [markRead description]
     *
     * @return  [type]  [return description]
     */
    public function markRead() {
        (new AdminNotice)->where('admin_id', admin()->user()->id)->update(['status' => 1]);
        return redirect()->back();
    }

    public function url(string $type, string $typeId) {
        (new AdminNotice)
        ->where('admin_id', admin()->user()->id)
        ->where('type', $type)
        ->where('type_id', $typeId)
        ->update(['status' => 1]);
        if ((in_array($type, ['pz_order_created', 'pz_order_success', 'pz_order_update_status']))) {
            return redirect(pz_route_admin('admin_order.detail', ['id' => $typeId]));
        }
        if ((in_array($type, ['pz_customer_created']))) {
            return redirect(pz_route_admin('admin_customer.edit', ['id' => $typeId]));
        }
    }
}
