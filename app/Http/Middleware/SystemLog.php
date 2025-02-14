<?php

namespace App\Http\Middleware;

use App\Http\Services\annotation\ControllerAnnotation;
use App\Http\Services\annotation\MiddlewareAnnotation;
use App\Http\Services\annotation\NodeAnnotation;
use App\Http\Services\SystemLogService;
use Closure;
use Illuminate\Http\Request;

/**
 * 系统操作日志中间件
 * Class SystemLog
 * @package app\admin\middleware
 */
class SystemLog
{

    /**
     * 敏感信息字段，日志记录时需要加密
     * @var array
     */
    protected array $sensitiveParams = [
        'password',
        'password_again',
        'phone',
        'mobile',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if (!config('easyadmin.ADMIN_SYSTEM_LOG')) return $response;
        if (!$request->ajax()) return $response;
        $method = $request->method();
        if (!in_array($method, ['POST', 'PUT', 'DELETE'])) return $response;
        $params = $request->input();
        if (isset($params['s'])) unset($params['s']);
        foreach ($params as $key => $val) {
            in_array($key, $this->sensitiveParams) && $params[$key] = "***********";
        }
        $method = strtolower($method);
        $url    = $request->getPathInfo();
        $title  = '';
        try {
            $currentAdminAction        = currentAdminAction();
            $currentAdminActionExplode = explode('@', $currentAdminAction);
            if ($currentAdminActionExplode) {
                $reflectionMethod = new \ReflectionMethod($currentAdminActionExplode[0], $currentAdminActionExplode[1]);
                $attributes       = $reflectionMethod->getAttributes(MiddlewareAnnotation::class);
                foreach ($attributes as $attribute) {
                    $annotation = $attribute->newInstance();
                    $_ignore    = (array)$annotation->ignore;
                    if (in_array('log', array_map('strtolower', $_ignore))) return $response;
                }
                $controllerTitle      = $nodeTitle = '';
                $controllerAttributes = (new \ReflectionClass($currentAdminActionExplode[0]))->getAttributes(ControllerAnnotation::class);
                $actionAttributes     = $reflectionMethod->getAttributes(NodeAnnotation::class);
                foreach ($controllerAttributes as $controllerAttribute) {
                    $controllerAnnotation = $controllerAttribute->newInstance();
                    $controllerTitle      = $controllerAnnotation->title ?? '';
                }
                foreach ($actionAttributes as $actionAttribute) {
                    $actionAnnotation = $actionAttribute->newInstance();
                    $nodeTitle        = $actionAnnotation->title ?? '';
                }
                $title = $controllerTitle . ' - ' . $nodeTitle;
            }
        }catch (\Throwable $exception) {
        }
        $ip        = $request->ip();
        $_response = json_encode($response->original, JSON_UNESCAPED_UNICODE);
        $_response = mb_substr($_response, 0, 3000, 'utf-8');
        $data      = [
            'admin_id'    => request()->session()->get('admin.id'),
            'title'       => $title,
            'url'         => $url,
            'method'      => $method,
            'ip'          => $ip,
            'content'     => json_encode($params, JSON_UNESCAPED_UNICODE),
            'response'    => $_response,
            'useragent'   => $request->header('HTTP_USER_AGENT'),
            'create_time' => time(),
        ];
        SystemLogService::instance()->save($data);
        return $response;
    }

}
