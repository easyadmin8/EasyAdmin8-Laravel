<?php

namespace App\Http\Controllers\admin\website;

use App\Http\Controllers\common\AdminController;
use App\Http\Services\TriggerService;
use App\Models\SystemConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Services\annotation\ControllerAnnotation;
use App\Http\Services\annotation\NodeAnnotation;

#[ControllerAnnotation(title: '官网站点配置')]
class ConfigController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new SystemConfig();
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
        $post = request()->post();
        $group = $post['group'] ?? '';
        if (empty($group)) return $this->error('分组不能为空');
        try {
            foreach ($post as $key => $value) {
                if (in_array($key, ['_token', 'file', 'group'])) continue;
                if ($this->model->where(['group' => $group, 'name' => $key])->exists()) {
                    $this->model->where(['group' => $group, 'name' => $key])->update(['value' => $value]);
                } else {
                    $this->model->insert(['group' => $group, 'name' => $key, 'value' => $value]);
                }
            }
            TriggerService::updateSysconfig();
        } catch (\Throwable $e) {
            return $this->error('保存失败:' . $e->getMessage());
        }
        return $this->success('保存成功');
    }
}
