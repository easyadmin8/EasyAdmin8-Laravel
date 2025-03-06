<?php

namespace App\Http\Middleware;

use App\Http\JumpTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Wolfcode\RateLimiting\Bootstrap;

class RateLimiting
{
    use JumpTrait;

    /**
     * 启用限流器需要开启Redis
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 是否启用限流器
        if (!config('easyadmin.RATE_LIMITING_STATUS', false)) return $next($request);
        if ($request->method() == 'GET') return $next($request);

        try {
            $currentAdminAction        = currentAdminAction();
            $currentAdminActionExplode = explode('@', $currentAdminAction);
            $controllerClass           = $currentAdminActionExplode[0];
            $action                    = $currentAdminActionExplode[1];
            Bootstrap::init($controllerClass, $action, [
                # Redis 相关配置
                'host'     => env('REDIS_HOST', '127.0.0.1'),
                'port'     => (int)env('REDIS_PORT', 6379),
                'password' => env('REDIS_PASSWORD', ''),
                'prefix'   => env('REDIS_PREFIX', ''),
                'database' => (int)env('REDIS_DATABASE', 0),
            ]);
        }catch (\Throwable $exception) {
            return (request()->ajax() || request()->method() == 'POST') ? $this->error($exception->getMessage()) : $this->responseView($exception->getMessage());
        }
        return $next($request);
    }
}
