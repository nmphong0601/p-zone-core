<?php
namespace PZone\Core\Admin\Controllers\Auth;

use PZone\Core\Admin\Models\AdminPermission;
use PZone\Core\Admin\Controllers\RootAdminController;
use Illuminate\Support\Str;
use Validator;

class PermissionController extends RootAdminController
{
    public $routeAdmin;

    public function __construct()
    {
        parent::__construct();
        
        $routes = app()->routes->getRoutes();

        foreach ($routes as $route) {
            if (Str::startsWith($route->uri(), PZ_ADMIN_PREFIX)) {
                $prefix = ltrim($route->getPrefix(), '/');
                $routeAdmin[$prefix] = [
                    'uri'    => 'ANY::' . $prefix . '/*',
                    'name'   => $prefix . '/*',
                    'method' => 'ANY',
                ];
                foreach ($route->methods as $key => $method) {
                    if ($method != 'HEAD' && !collect($this->without())->first(function ($exp) use ($route) {
                        return Str::startsWith($route->uri, $exp);
                    })) {
                        $routeAdmin[] = [
                            'uri'    => $method . '::' . $route->uri,
                            'name'   => $route->uri,
                            'method' => $method,
                        ];
                    }
                }
            }
        }

        $this->routeAdmin = $routeAdmin;
    }

    public function index()
    {
        $data = [
            'title' => pz_language_render('admin.permission.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => pz_route_admin('admin_permission.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
        ];
        //Process add content
        $data['menuRight'] = pz_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = pz_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = pz_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = pz_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = pz_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'id' => 'ID',
            'slug' => pz_language_render('admin.permission.slug'),
            'name' => pz_language_render('admin.permission.name'),
            'http_path' => pz_language_render('admin.permission.http_path'),
            'updated_at' => pz_language_render('admin.updated_at'),
            'action' => pz_language_render('action.title'),
        ];
        $sort_order = pz_clean(request('sort_order') ?? 'id_desc');
        $arrSort = [
            'id__desc' => pz_language_render('filter_sort.id_desc'),
            'id__asc' => pz_language_render('filter_sort.id_asc'),
            'name__desc' => pz_language_render('filter_sort.name_desc'),
            'name__asc' => pz_language_render('filter_sort.name_asc'),
        ];
        $obj = new AdminPermission;
        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $obj = $obj->orderBy($field, $sort_field);
        } else {
            $obj = $obj->orderBy('id', 'desc');
        }
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $permissions = '';
            if ($row['http_uri']) {
                $methods = array_map(function ($value) {
                    $route = explode('::', $value);
                    $methodStyle = '';
                    if ($route[0] == 'ANY') {
                        $methodStyle = '<span class="badge badge-info">' . $route[0] . '</span>';
                    } elseif ($route[0] == 'POST') {
                        $methodStyle = '<span class="badge badge-warning">' . $route[0] . '</span>';
                    } else {
                        $methodStyle = '<span class="badge badge-primary">' . $route[0] . '</span>';
                    }
                    return $methodStyle . ' <code>' . $route[1] . '</code>';
                }, explode(',', $row['http_uri']));
                $permissions = implode('<br>', $methods);
            }
            $dataTr[] = [
                'id' => $row['id'],
                'slug' => $row['slug'],
                'name' => $row['name'],
                'permission' => $permissions,
                'updated_at' => $row['updated_at'],
                'action' => '
                    <a href="' . pz_route_admin('admin_permission.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . pz_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                    <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . pz_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>'
                ,
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = pz_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a href="' . pz_route_admin('admin_permission.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
                           <i class="fa fa-plus" title="'.pz_language_render('action.add').'"></i>
                           </a>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $data['urlSort'] = pz_route_admin('admin_permission.index', request()->except(['_token', '_pjax', 'sort_order']));
        $data['optionSort'] = $optionSort;
        //=menuSort

        return view($this->templatePathAdmin.'screen.list')
            ->with($data);
    }

    /**
     * Form create new item in admin
     * @return [type] [description]
     */
    public function create()
    {
        $data = [
            'title' => pz_language_render('admin.permission.add_new_title'),
            'subTitle' => '',
            'title_description' => pz_language_render('admin.permission.add_new_des'),
            'icon' => 'fa fa-plus',
            'permission' => [],
            'routeAdmin' => $this->routeAdmin,
            'url_action' => pz_route_admin('admin_permission.create'),

        ];

        return view($this->templatePathAdmin.'auth.permission')
            ->with($data);
    }

    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate()
    {
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'name' => 'required|string|max:50|unique:"'.AdminPermission::class.'",name',
            'slug' => 'required|regex:/(^([0-9A-Za-z\._\-]+)$)/|unique:"'.AdminPermission::class.'",slug|string|max:50|min:3',
        ], [
            'slug.regex' => pz_language_render('admin.permission.slug_validate'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dataCreate = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'http_uri' => implode(',', ($data['http_uri'] ?? [])),
        ];
        $dataCreate = pz_clean($dataCreate, [], true);
        $permission = AdminPermission::createPermission($dataCreate);

        return redirect()->route('admin_permission.index')->with('success', pz_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $permission = AdminPermission::find($id);
        if ($permission === null) {
            return 'no data';
        }
        $data = [
            'title' => pz_language_render('action.edit'),
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-edit',
            'permission' => $permission,
            'routeAdmin' => $this->routeAdmin,
            'url_action' => pz_route_admin('admin_permission.edit', ['id' => $permission['id']]),
        ];
        return view($this->templatePathAdmin.'auth.permission')
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id)
    {
        $permission = AdminPermission::find($id);
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'name' => 'required|string|max:50|unique:"'.AdminPermission::class.'",name,' . $permission->id . '',
            'slug' => 'required|regex:/(^([0-9A-Za-z\._\-]+)$)/|unique:"'.AdminPermission::class.'",slug,' . $permission->id . '|string|max:50|min:3',
        ], [
            'slug.regex' => pz_language_render('admin.permission.slug_validate'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit

        $dataUpdate = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'http_uri' => implode(',', ($data['http_uri'] ?? [])),
        ];
        $dataUpdate = pz_clean($dataUpdate, [], true);
        $permission->update($dataUpdate);
        return redirect()->route('admin_permission.index')->with('success', pz_language_render('action.edit_success'));
    }

    /*
    Delete list Item
    Need mothod destroy to boot deleting in model
     */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => pz_language_render('admin.method_not_allow')]);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            AdminPermission::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function without()
    {
        $prefix = PZ_ADMIN_PREFIX?PZ_ADMIN_PREFIX.'/':'';
        return [
            $prefix . 'login',
            $prefix . 'logout',
            $prefix . 'forgot',
            $prefix . 'deny',
            $prefix . 'locale',
            $prefix . 'uploads',
        ];
    }
}
