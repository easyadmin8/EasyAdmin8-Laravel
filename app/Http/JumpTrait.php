<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

trait JumpTrait
{
    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $msg 提示信息
     * @param array $data 返回的数据
     * @param string|null $url 跳转的 URL 地址
     * @param int $wait 跳转等待时间
     * @return Response|JsonResponse|View
     */
    protected function success(string $msg = '', array $data = [], ?string $url = null, int $wait = 3): Response|JsonResponse|View
    {
        if (empty($msg)) $msg = ea_trans('operation successful');
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        }elseif ($url) {
            $url = (strpos($url, '://') || str_starts_with($url, '/')) ? $url : app('route')->buildUrl($url)->__toString();
        }
        if (empty($url)) $url = __url();
        $result = [
            'code'      => 1,
            'msg'       => $msg,
            'data'      => $data,
            'url'       => $url,
            'wait'      => $wait,
            '__token__' => csrf_token(),
        ];
        if ($this->getResponseType() == "html") return view('admin.success', $result);
        return response()->json($result);
    }

    /**
     * @param string $msg
     * @param array $data
     * @param string|null $url
     * @param int $wait
     * @return Response|JsonResponse|View
     */
    public function error(string $msg = '', array $data = [], ?string $url = null, int $wait = 3): Response|JsonResponse|View
    {
        if (empty($msg)) $msg = ea_trans('operation failed');
        if (is_null($url)) {
            $url = request()->ajax() ? '' : 'javascript:history.back(-1);';
        }elseif ($url) {
            $url = (strpos($url, '://') || str_starts_with($url, '/')) ? $url : "";
        }
        $result = [
            'code'      => 0,
            'msg'       => $msg,
            'data'      => $data,
            'url'       => $url,
            'wait'      => $wait,
            '__token__' => csrf_token(),
        ];
        if ($this->getResponseType() == "html") return view('admin.error', $result);
        return response()->json($result);
    }

    /**
     * @param string $msg
     * @param array $data
     * @param string|null $url
     * @param int $wait
     * @return Response|JsonResponse|View
     */
    public function responseView(string $msg = '', array $data = [], ?string $url = null, int $wait = 3): Response|JsonResponse|View
    {
        if (empty($msg)) $msg = ea_trans('operation failed');
        if (is_null($url)) {
            $url = request()->ajax() ? '' : 'javascript:history.back(-1);';
        }elseif ($url) {
            $url = (strpos($url, '://') || str_starts_with($url, '/')) ? $url : "";
        }
        $result = [
            'code'      => 0,
            'msg'       => $msg,
            'data'      => $data,
            'url'       => $url,
            'wait'      => $wait,
            '__token__' => csrf_token(),
        ];
        if ($this->getResponseType() == "html") return response()->view('admin.error', $result);
        return response()->json($result);
    }

    /**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType(): string
    {
        return (request()->ajax() || request()->method() == 'POST') ? 'json' : 'html';
    }

}
