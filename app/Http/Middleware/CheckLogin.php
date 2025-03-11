<?php

namespace App\Http\Middleware;

use App\Http\JumpTrait;
use App\Http\Services\annotation\MiddlewareAnnotation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
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
        $response    = $next($request);
        $adminConfig = config('admin');
        $parameters  = request()->route()->parameters;
        $controller  = $parameters['controller'] ?? 'index';
        $secondary   = '';
        if (!empty($parameters['secondary'])) $secondary = $parameters['secondary'];
        if (!in_array($controller, $adminConfig['no_login_controller'])) {
            $adminNamespace = config('admin.controller_namespace');
            $namespace      = $adminNamespace . ($secondary ? $secondary . '\\' : '');
            $className      = $namespace . ucfirst($controller . "Controller");
            try {
                $classObj   = new \ReflectionClass($className);
                $properties = $classObj->getDefaultProperties();
                // 整个控制器是否忽略登录
                $ignoreLogin = $properties['ignoreLogin'] ?? false;
                if ($ignoreLogin) return $response;
                if (!empty($parameters['action'])) {
                    $reflectionMethod = new \ReflectionMethod($className, $parameters['action']);
                    $attributes       = $reflectionMethod->getAttributes(MiddlewareAnnotation::class);
                    foreach ($attributes as $attribute) {
                        $annotation = $attribute->newInstance();
                        $_ignore    = (array)$annotation->ignore;
                        // 控制器中的某个方法忽略登录
                        if (in_array('LOGIN', $_ignore)) return $next($request);
                    }
                }
            }catch (\ReflectionException $e) {
            }

            $adminId    = session('admin.id', 0);
            $expireTime = session('admin.expire_time');
            if (empty($adminId)) {
                return $this->responseView(ea_trans('Please log in to the backend first', false), [], __url("/login"));
            }
            // 判断是否登录过期
            if ($expireTime !== true && time() > $expireTime) {
                $request->session()->forget('admin');
                return $this->responseView(ea_trans('Login has expired, please log in again', false), [], __url("/login"));
            }
        }
        return $response;
    }
}
