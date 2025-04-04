<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * 首页的PID
 */
const HOME_PID       = 99999999;
const SUPER_ADMIN_ID = 1;
/**
 * @param string $url
 * @param array $vars
 * @param false $suffix
 * @return string
 */
function __url(string $url = '', array $vars = [], bool $suffix = false): string
{
    $url  = config('admin')['admin_alias_name'] . (str_starts_with($url, '/') ? $url : "/{$url}");
    $_url = url($url, $vars, $suffix);
    return explode(request()->schemeAndHttpHost(), $_url)[1] ?? '/' . $url;

}

if (!function_exists('parse_name')) {
    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @param string $name 字符串
     * @param int $type 转换类型
     * @param bool $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    function parse_name(string $name, int $type = 0, bool $ucfirst = true): string
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function($match) {
                return strtoupper($match[1]);
            }, $name);

            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace('/[A-Z]/', '_\\0', $name), '_'));
    }
}

if (!function_exists('sysconfig')) {

    /**
     * 获取系统配置信息
     * @param $group
     * @param null $name
     * @return mixed
     */
    function sysconfig($group, $name = null): mixed
    {
        $where = ['group' => $group];
        $value = empty($name) ? Cache::get("sysconfig_{$group}") : Cache::get("sysconfig_{$group}_{$name}");
        if (empty($value)) {
            if (!empty($name)) {
                $where['name'] = $name;
                $value         = \App\Models\SystemConfig::where($where)->value('value');
                Cache::put("sysconfig_{$group}_{$name}", $value, 3600);
            }else {
                $res   = \App\Models\SystemConfig::where($where)->select('value', 'name')->get()->toArray();
                $value = collect($res)->pluck('value', 'name')->toArray();
                Cache::put("sysconfig_{$group}", $value, 3600);
            }
        }
        return $value;
    }
}

if (!function_exists('auths')) {

    /**
     * auth权限验证
     * @param $node
     * @return bool
     */
    function auths($node = null): bool
    {
        $authService = new \App\Http\Services\AuthService(session('admin.id'));
        return $authService->checkNode($node);
    }

}

if (!function_exists('password')) {

    /**
     * 密码加密算法
     * @param string $value 需要加密的值
     * @return string
     */
    function password(string $value): string
    {
        $value = sha1('blog_') . md5($value) . md5('_encrypt') . sha1($value);
        return sha1($value);
    }

}

if (!function_exists('json')) {

    function json(array $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json($data);
    }

}

if (!function_exists('insertFields')) {

    function insertFields($model, array $params = [])
    {
        $post = request()->post();
        if (!empty($params)) $post += $params;
        $fields = Schema::getColumnListing($model->getTable());
        if (in_array('create_time', $fields)) $post['create_time'] = time();
        $tableColumn = array_keys($post);
        $fields      = array_intersect($tableColumn, $fields);
        foreach ($fields as $value) {
            if (isset($params[$value])) $post[$value] = $params[$value];
            $model->$value = $post[$value];
        }
        return $model->save();
    }

}

if (!function_exists('updateFields')) {

    function updateFields($model, $row, array $params = [])
    {
        $post = request()->post();
        if (!empty($params)) $post += $params;
        $fields = Schema::getColumnListing($model->getTable());
        if (in_array('update_time', $fields)) $post['update_time'] = time();
        $tableColumn = array_keys($post);
        $fields      = array_intersect($tableColumn, $fields);
        $dirty       = $row->getDirty();
        foreach ($fields as $value) {
            if (isset($params[$value])) $post[$value] = $params[$value];
            if (!isset($dirty[$value])) {
                $row->$value = $post[$value];
            }
        }
        return $row->save();
    }

}

if (!function_exists('currentAdminAction')) {
    function currentAdminAction(?string $action = null): string
    {
        $request    = request();
        $controller = $request->controller ?: 'Index';
        $action     = $request->action ?: 'index';
        $name       = $request->route()->parameter('secondary');
        $controller = ucfirst($controller);
        $_name      = '';
        switch ($action) {
            case 'controller':
                if (empty($_controller)) $_controller = ucfirst($_name);
                return $_controller;
            case 'action':
                return $action;
            default:
                if ($name) {
                    $namespace = "App\Http\Controllers\admin\\{$name}\\{$controller}Controller@{$action}";
                }else {
                    $namespace = "App\Http\Controllers\admin\\{$controller}Controller@{$action}";
                }
                return $namespace;
        }
    }
}

/**
 * @param string|null $detail
 * @param string $name
 * @param string $placeholder
 * @return string
 */
function editor_textarea(?string $detail, string $name = 'desc', string $placeholder = '请输入'): string
{
    $editor_type = sysconfig('site', 'editor_type');
    return match ($editor_type) {
        'ckeditor' => "<textarea name='{$name}' rows='20' class='layui-textarea editor' placeholder='{$placeholder}'>{$detail}</textarea>",
        'ueditor'  => "<script type='text/plain' id='{$name}' name='{$name}' class='editor' data-content='{$detail}'></script>",
        'EasyMDE'  => "<textarea id='{$name}' class='editor' name='{$name}'>{$detail}</textarea>",
        default    => "<div class='wangEditor_div'><textarea name='{$name}' rows='20' class='layui-textarea editor layui-hide'>{$detail}</textarea><div id='editor_toolbar_{$name}'></div><div id='editor_{$name}' style='height: 300px'></div></div>",
    };
}
