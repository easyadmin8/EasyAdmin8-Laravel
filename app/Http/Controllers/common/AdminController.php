<?php

namespace App\Http\Controllers\common;

use App\Http\Curd;
use App\Http\JumpTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    use JumpTrait;
    use Curd;

    /**
     * 是否为演示环境
     * @var bool
     */
    protected bool $isDemo = false;

    /**
     * @Model
     */
    protected object $model;

    /**
     * @var string
     */
    public string $order = 'id';

    /**
     * @var string
     */
    public string $orderDirection = 'desc';

    /**
     * 过滤节点更新
     * @var array
     */
    protected array $ignoreNode = [];

    /**
     * 不导出的字段信息
     * @var array
     */
    protected array $noExportFields = ['delete_time', 'update_time'];

    /**
     * @var string
     */
    public string $secondary = '';

    /**
     * @var string
     */
    public string $controller = '';

    /**
     * @var string
     */
    public string $action = '';

    /**
     * @var array
     */
    public array $adminConfig = [];

    protected function initialize()
    {
        $parameters           = request()->route()->parameters ?? [];
        $this->adminConfig    = $adminConfig = config('admin');
        $this->isDemo         = config('easyadmin.IS_DEMO', false);
        $secondary            = $parameters['secondary'] ?? '';
        $controller           = $parameters['controller'] ?? 'index';
        $action               = $parameters['action'] ?? 'index';
        $this->secondary      = $secondary;
        $this->controller     = $controller;
        $this->action         = $action;
        $jsBasePath           = ($secondary ? "{$secondary}/" : '') . strtolower($controller);
        $thisControllerJsPath = "admin/js/{$jsBasePath}.js";
        $autoloadJs           = file_exists($thisControllerJsPath);
        $adminModuleName      = $adminConfig['admin_alias_name'];
        $isSuperAdmin         = session('admin.id') == $adminConfig['super_admin_id'];
        $version              = cache('version');
        if (empty($version)) {
            $version = sysconfig('site', 'site_version');
            cache('site_version', $version);
            Cache::set('version', $version, 3600);
        }
        $data = [
            'adminModuleName'      => $adminModuleName,
            'thisController'       => $controller,
            'thisAction'           => $action,
            'thisRequest'          => "{$adminModuleName}/{$controller}/{$action}",
            'thisControllerJsPath' => $thisControllerJsPath,
            'autoloadJs'           => $autoloadJs,
            'isSuperAdmin'         => $isSuperAdmin,
            'isDemo'               => $this->isDemo,
            'version'              => config('app.debug') ? time() : $version,
            'adminUploadUrl'       => __url('ajax/upload', [], false),
            'adminEditor'          => sysconfig('site', 'editor_type') ?: 'wangEditor',
        ];
        $this->assign($data);
        $this->setOrder();
    }

    /**
     * 初始化排序
     * @return $this
     */
    public function setOrder(): static
    {
        $tableOrder = request()->get('tableOrder', '');
        if (!empty($tableOrder)) {
            [$orderField, $orderType] = explode(' ', $tableOrder);
            $this->order          = $orderField;
            $this->orderDirection = $orderType;
        }
        return $this;
    }

    /**
     * @param array $args
     */
    public function assign(array $args = [])
    {
        \Illuminate\Support\Facades\View::share($args);
    }

    public function fetch(string $template = '', array $args = []): View
    {
        $adminConfig     = config('admin');
        $jsTrans         = __("messages.{$adminConfig['admin_alias_name']}.js") + ea_trans('', true, 'common');
        $args['jsTrans'] = $jsTrans;
        if (empty($template)) {
            $basePath = ".{$this->controller}.{$this->action}";
            if ($this->secondary) {
                $template = 'admin.' . $this->secondary . $basePath;
            }else {
                $template = 'admin' . $basePath;
            }
        }
        return view($template, $args);
    }

    /**
     * 构建请求参数
     * @param array $excludeFields 忽略构建搜索的字段
     * @return array
     */
    protected function buildTableParams(array $excludeFields = []): array
    {
        $get     = request()->input();
        $page    = !empty($get['page']) ? $get['page'] : 1;
        $limit   = !empty($get['limit']) ? $get['limit'] : 15;
        $filters = !empty($get['filter']) ? htmlspecialchars_decode($get['filter']) : '{}';
        $ops     = !empty($get['op']) ? htmlspecialchars_decode($get['op']) : '{}';
        // json转数组
        $filters  = json_decode($filters, true);
        $ops      = json_decode($ops, true);
        $where    = [];
        $excludes = [];

        foreach ($filters as $key => $val) {
            if (in_array($key, $excludeFields)) {
                $excludes[$key] = $val;
                continue;
            }
            $op = !empty($ops[$key]) ? $ops[$key] : '%*%';

            switch (strtolower($op)) {
                case '=':
                    $where[] = [$key, '=', $val];
                    break;
                case '%*%':
                    $where[] = [$key, 'LIKE', "%{$val}%"];
                    break;
                case '*%':
                    $where[] = [$key, 'LIKE', "{$val}%"];
                    break;
                case '%*':
                    $where[] = [$key, 'LIKE', "%{$val}"];
                    break;
                case 'in':
                    $where[] = [DB::raw("$key IN ($val)"), 1];
                    break;
                case 'range':
                    [$beginTime, $endTime] = explode(' - ', $val);
                    $where[] = [$key, '>=', strtotime($beginTime)];
                    $where[] = [$key, '<=', strtotime($endTime)];
                    break;
                default:
                    $where[] = [$key, $op, "%{$val}"];
            }
        }
        return [$page, $limit, $where, $excludes];
    }

    /**
     * 下拉选择列表
     * @return JsonResponse
     */
    public function selectList(): JsonResponse
    {
        $fields = request()->input('selectFields');
        $data   = $this->model->select(explode(',', $fields))->get()->toArray();
        return $this->success('', $data);
    }

}
