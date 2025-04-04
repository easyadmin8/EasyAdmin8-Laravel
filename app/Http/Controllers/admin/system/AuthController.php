<?php

namespace App\Http\Controllers\admin\system;

use App\Http\Controllers\common\AdminController;
use App\Http\Services\TriggerService;
use App\Models\SystemAuth;
use App\Models\SystemAuthNode;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Services\annotation\NodeAnnotation;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '角色权限管理')]
class AuthController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new SystemAuth();
    }

    #[NodeAnnotation(title: '授权', auth: true)]
    public function authorizes(): View|JsonResponse
    {
        $id  = request()->input('id');
        $row = $this->model->find($id);
        if (empty($row)) return $this->error('数据不存在');
        if (request()->ajax()) {
            $list = $this->model->getAuthorizeNodeListByAdminId($id);
            return $this->success('获取成功', $list);
        }
        $this->assign(compact('row'));
        return $this->fetch();
    }

    #[NodeAnnotation(title: '授权保存', auth: true)]
    public function saveAuthorize(): JsonResponse
    {
        if (!request()->ajax()) return $this->error();
        $id   = request()->input('id');
        $node = request()->post('node', "[]");
        $node = json_decode($node, true);
        $row  = $this->model->find($id);
        if (empty($row)) return $this->error('数据不存在');
        try {
            $authNode = new SystemAuthNode();
            $authNode->where('auth_id', $id)->delete();
            if (!empty($node)) {
                $saveAll = [];
                foreach ($node as $vo) {
                    $saveAll[] = [
                        'auth_id' => $id,
                        'node_id' => $vo,
                    ];
                }
                $authNode->addAll($saveAll);
            }
            TriggerService::updateMenu();
        }catch (\Exception $e) {
            return $this->error('保存失败:' . $e->getMessage());
        }
        return $this->success('保存成功');
    }
}
