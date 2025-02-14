<?php

namespace App\Http\Middleware;

use App\Http\JumpTrait;
use App\Http\Services\annotation\MiddlewareAnnotation;
use App\Http\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    use JumpTrait;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminConfig = config('admin');
        $parameters  = request()->route()->parameters;
        $controller  = $parameters['controller'] ?? 'index';
        $adminId     = session('admin.id', 0);
        try {
            $currentAdminAction        = currentAdminAction();
            $currentAdminActionExplode = explode('@', $currentAdminAction);
            $reflectionClass           = new \ReflectionMethod($currentAdminActionExplode[0], $currentAdminActionExplode[1]);
            $checkIgnoreLogin          = $reflectionClass->getAttributes(MiddlewareAnnotation::class)[0]->newInstance()->ignore;
            // 不需要登录的页面 跳过检测权限
            if (strtolower($checkIgnoreLogin) == 'login') return $next($request);
        }catch (\Throwable) {
        }
        // 验证权限
        if ($adminId) {
            $authService = app(AuthService::class, ['adminId' => $adminId]);
            $currentNode = $authService->getCurrentNode();
            if (!in_array($controller, $adminConfig['no_auth_controller']) && !in_array($controller, $adminConfig['no_auth_node'])) {
                $check = $authService->checkNode($currentNode);
                if (!$check) return (request()->ajax() || request()->method() == 'POST') ? $this->error('无权限访问') : $this->responseView('无权限访问');
                // 判断是否为演示环境
                if (config('easyadmin.IS_DEMO', false) && \request()->method() == 'POST') {
                    if (!in_array($currentNode, ['system.log/record', ''])) return (request()->ajax() || request()->method() == 'POST') ? $this->error('演示环境下不允许修改') : $this->responseView('无权限访问');
                }
            }
        }
        return $next($request);
    }
}
