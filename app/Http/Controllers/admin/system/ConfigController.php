<?php

namespace App\Http\Controllers\admin\system;

use App\Http\Controllers\common\AdminController;
use App\Http\Services\TriggerService;
use App\Models\SystemConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Services\annotation\NodeAnnotation;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '系统配置管理')]
class ConfigController extends AdminController
{

    public function initialize()
    {
        parent::initialize();
        $this->model  = new SystemConfig();
        $upload_types = config('admin.upload_types');
        $editor_types = config('admin.editor_types');
        $this->assign(compact('upload_types', 'editor_types'));
    }

    #[NodeAnnotation(title: '列表', auth: true)]
    public function index(): View
    {
        return $this->fetch();
    }

    #[NodeAnnotation(title: '保存', auth: true)]
    public function save(): JsonResponse
    {
        if (!request()->ajax()) return $this->error();
        $post         = request()->post();
        $notAddFields = ['_token', 'file', 'group'];
        try {
            $group = $post['group'] ?? '';
            if (empty($group)) return $this->error('保存失败');
            if ($group == 'upload') {
                $upload_types = config('admin.upload_types');
                // 兼容旧版本
                $this->model->where('name', 'upload_allow_type')->update(['value' => implode(',', array_keys($upload_types))]);
            }
            foreach ($post as $key => $val) {
                if (in_array($key, $notAddFields)) continue;
                if ($this->model->where('name', $key)->count()) {
                    $this->model->where('name', $key)->update(['value' => $val,]);
                }else {
                    $this->model->insert(
                        [
                            'name'  => $key,
                            'value' => $val,
                            'group' => $group,
                        ]);
                }
            }
            TriggerService::updateSysconfig();
        }catch (\Exception $e) {
            return $this->error('保存失败:' . $e->getMessage());
        }
        return $this->success('保存成功');
    }

}
