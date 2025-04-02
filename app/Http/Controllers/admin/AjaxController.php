<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\common\AdminController;
use App\Http\Services\MenuService;
use App\Http\Services\UploadService;
use App\Models\SystemUploadfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AjaxController extends AdminController
{
    /**
     * @desc 初始化导航
     * @return JsonResponse
     */
    public function initAdmin(): JsonResponse
    {
        $cacheData = Cache::get('initAdmin_' . session('admin.id'));
        if (!empty($cacheData)) {
            return json($cacheData);
        }
        $menuService = new MenuService(session('admin.id'));
        $data        = [
            'logoInfo' => [
                'title' => sysconfig('site', 'logo_title'),
                'image' => sysconfig('site', 'logo_image'),
                'href'  => __url(),
            ],
            'homeInfo' => $menuService->getHomeInfo(),
            'menuInfo' => $menuService->getMenuTree(),
        ];
        Cache::put('initAdmin_' . session('admin.id'), $data);
        return json($data);
    }

    /**
     * @desc 清理缓存接口
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        Cache::flush();
        return $this->success(ea_trans('Successfully cleared cache', true, 'common'));
    }

    /**
     * @desc  上传文件
     * @return View|JsonResponse
     */
    public function upload(): View|JsonResponse
    {
        if ($this->isDemo) return $this->error(ea_trans('Modification is not allowed in the demonstration environment', false));
        if (request()->method() != 'POST') return $this->error();
        $type         = request()->input('type', '');
        $data         = [
            'upload_type' => request()->post('upload_type', ''),
            'file'        => request()->file($type == 'editor' ? 'upload' : 'file'),
        ];
        $uploadConfig = sysconfig('upload');
        empty($data['upload_type']) && $data['upload_type'] = $uploadConfig['upload_type'];
        $rules     = [
            'upload_type' => ['required', Rule::in(explode(',', $uploadConfig['upload_allow_type']))],
            'file'        => 'required',
        ];
        $validator = Validator::make($data, $rules, [
            'upload_type' => ea_trans('The specified upload type is incorrect'),
            'file'        => ea_trans('The file cannot be empty'),
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $file = $type == 'editor' ? request()->upload : request()->file;
        if (!in_array($file->extension(), explode(',', $uploadConfig['upload_allow_ext']))) {
            return $this->error(ea_trans('File format is incorrect', true, 'common'));
        }
        if ($file->getSize() > $uploadConfig['upload_allow_size']) {
            return $this->error(ea_trans('File size exceeds the limit', true, 'common'));
        }
        $upload_type = $uploadConfig['upload_type'];
        try {
            $upload = UploadService::instance()->setConfig($uploadConfig)->$upload_type($file, $type);
        }catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        $code = $upload['code'] ?? 0;
        if ($code == 0) {
            return $this->error($upload['data'] ?? '');
        }else {
            return $type == 'editor' ? json(
                [
                    'error'    => ['message' => ea_trans('Upload successful', true, 'common'), 'number' => 201,],
                    'fileName' => '',
                    'uploaded' => 1,
                    'url'      => $upload['data']['url'] ?? '',
                ]
            ) : $this->success(ea_trans('Upload successful', true, 'common'), $upload['data'] ?? '');
        }
    }

    /**
     * @desc 获取上传文件
     * @return JsonResponse
     */
    public function getUploadFiles(): JsonResponse
    {
        $get         = request()->input();
        $limit       = $get['limit'] ?? 10;
        $title       = $get['title'] ?? '';
        $this->model = new SystemUploadfile();
        $where       = [];
        if ($title) $where[] = ['original_name', 'LIKE', "%{$title}%"];
        $count = $this->model->where($where)->count();
        $list  = $this->model->where($where)->orderBy($this->order, $this->orderDirection)->paginate($limit)->items();
        $data  = [
            'code'  => 0,
            'msg'   => '',
            'count' => $count,
            'data'  => $list,
        ];
        return json($data);
    }

    /**
     * @desc 百度编辑器上传
     * @return JsonResponse
     */
    public function uploadUEditor(): JsonResponse
    {
        $uploadConfig      = sysconfig('upload');
        $upload_allow_size = $uploadConfig['upload_allow_size'];
        $_upload_allow_ext = explode(',', $uploadConfig['upload_allow_ext']);
        $upload_allow_ext  = [];
        array_map(function ($value) use (&$upload_allow_ext) {
            $upload_allow_ext[] = '.' . $value;
        }, $_upload_allow_ext);
        $config      = [
            // 上传图片配置项
            "imageActionName"         => "image",
            "imageFieldName"          => "file",
            "imageMaxSize"            => $upload_allow_size,
            "imageAllowFiles"         => $upload_allow_ext,
            "imageCompressEnable"     => true,
            "imageCompressBorder"     => 5000,
            "imageInsertAlign"        => "none",
            "imageUrlPrefix"          => "",
            // 列出图片
            "imageManagerActionName"  => "listImage",
            "imageManagerListSize"    => 20,
            "imageManagerUrlPrefix"   => "",
            "imageManagerInsertAlign" => "none",
            "imageManagerAllowFiles"  => $upload_allow_ext,
            // 上传 video
            "videoActionName"         => "video",
            "videoFieldName"          => "file",
            "videoUrlPrefix"          => "",
            "videoMaxSize"            => $upload_allow_size,
            "videoAllowFiles"         => $upload_allow_ext,
            // 上传 附件
            "fileActionName"          => "attachment",
            "fileFieldName"           => "file",
            "fileMaxSize"             => $upload_allow_size,
            "fileAllowFiles"          => $upload_allow_ext,
        ];
        $action      = request()->input('action', '');
        $file        = request()->file('file');
        $upload_type = $uploadConfig['upload_type'];
        switch ($action) {
            case 'image':
            case 'attachment':
            case 'video':
                if ($this->isDemo) return json(['state' => ea_trans('Modification is not allowed in the demonstration environment', false)]);
                try {
                    $upload = UploadService::instance()->setConfig($uploadConfig)->$upload_type($file);
                    $code   = $upload['code'] ?? 0;
                    if ($code == 0) {
                        return json(['state' => $upload['data'] ?? 'error']);
                    }else {
                        return json(['state' => 'SUCCESS', 'url' => $upload['data']['url'] ?? '']);
                    }
                }catch (\Exception $e) {
                    return $this->error($e->getMessage());
                }
            case 'listImage':
                $list   = (new SystemUploadfile())->select('url')->orderByDesc('id')->paginate(100)->items();
                $result = [
                    "state" => "SUCCESS",
                    "list"  => $list,
                    "total" => 0,
                    "start" => 0,
                ];
                return json($result);
            default:
                return json($config);
        }
    }
}
