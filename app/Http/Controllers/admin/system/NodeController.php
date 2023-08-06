<?php

namespace App\Http\Controllers\admin\system;

use App\Http\Controllers\common\AdminController;
use App\Http\Services\NodeService;
use App\Http\Services\TriggerService;
use App\Models\SystemNode;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Http\Services\annotation\NodeAnnotation;
use App\Http\Services\annotation\ControllerAnnotation;

/**
 * @ControllerAnnotation(title="系统节点管理")
 */
class NodeController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new SystemNode();
    }

    /**
     * @NodeAnnotation(title="列表")
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $count = $this->model->count();
            $list  = $this->model->getNodeTreeList();
            $data  = [
                'code'  => 0,
                'msg'   => '',
                'count' => $count,
                'data'  => $list,
            ];
            return json($data);
        }
        return $this->fetch();
    }

    /**
     * @NodeAnnotation(title="系统节点更新")
     */
    public function refreshNode(): JsonResponse
    {
        $force = request()->input('force');
        if (!request()->ajax()) return $this->error();
        $nodeList = (new NodeService())->getNodeList();
        if (empty($nodeList)) return $this->error('暂无需要更新的系统节点');
        $model = new SystemNode();
        try {
            if ($force == 1) {
                $updateNodeList = $model->where('node', 'IN', array_column($nodeList, 'node'))->get()->toArray();
                $formatNodeList = [];
                array_map(function ($value) use (&$formatNodeList) {
                    $formatNodeList[$value['node']] = $value['title'];
                }, $nodeList);
                foreach ($updateNodeList as $vo) {
                    if (isset($formatNodeList[$vo['node']])) {
                        $model->where('id', $vo['id'])->update(
                            [
                                'title'   => $formatNodeList[$vo['node']]['title'],
                                'is_auth' => $formatNodeList[$vo['node']]['is_auth'],
                            ]
                        );
                    }
                }
            }
            $existNodeList = $model->select(explode(',', 'node,title,type,is_auth'))->get()->toArray();
            foreach ($nodeList as $key => &$vo) {
                $vo['create_time'] = $vo['update_time'] = time();
                foreach ($existNodeList as $v) {
                    if ($vo['node'] == $v['node']) {
                        unset($nodeList[$key]);
                        break;
                    }
                }
            }
            $model->addAll($nodeList);
            TriggerService::updateNode();
        } catch (\Exception $e) {
            return $this->error('节点更新失败:' . $e->getMessage());
        }
        return $this->success('节点更新成功');
    }

    /**
     * @NodeAnnotation(title="清除失效节点")
     */
    public function clearNode(): JsonResponse
    {
        if (!request()->ajax()) return $this->error();
        $nodeList = (new NodeService())->getNodeList();
        $model    = new SystemNode();
        try {
            $existNodeList  = $model->select(explode(',', 'id,node,title,type,is_auth'))->get()->toArray();
            $formatNodeList = [];
            array_map(function ($value) use (&$formatNodeList) {
                $formatNodeList[$value['node']] = $value['title'];
            }, $nodeList);
            foreach ($existNodeList as $vo) {
                !isset($formatNodeList[$vo['node']]) && $model->where('id', $vo['id'])->delete();
            }
            TriggerService::updateNode();
        } catch (\Exception $e) {
            return $this->error('节点更新失败:' . $e->getMessage());
        }
        return $this->success('节点更新成功');
    }
}
