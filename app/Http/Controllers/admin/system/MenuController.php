<?php

namespace App\Http\Controllers\admin\system;

use App\Http\Controllers\common\AdminController;
use App\Http\Services\TriggerService;
use App\Models\SystemMenu;
use App\Models\SystemNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use App\Http\Services\annotation\NodeAnnotation;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '菜单管理')]
class MenuController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new SystemMenu();
    }

    #[NodeAnnotation(title: '添加', auth: true)]
    public function add(): View|JsonResponse
    {
        $id     = request()->input('id');
        $homeId = $this->model->where(['pid' => HOME_PID,])->value('id');
        if ($id == $homeId) {
            return $this->error('首页不能添加子菜单');
        }
        if (request()->ajax()) {
            $post      = request()->post();
            $rules     = [
                'pid'    => 'required',
                'title'  => 'required',
                'icon'   => 'required',
                'target' => 'required',
            ];
            $validator = Validator::make($post, $rules, [
                'pid'    => '上级菜单不能为空',
                'title'  => '菜单名称不能为空',
                'icon'   => '菜单图标不能为空',
                'target' => 'target属性不能为空',
            ]);
            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }
            $params = [];
            if (empty($post['href'])) $params['href'] = '';
            try {
                $save = insertFields($this->model, $params);
            }catch (\Exception $e) {
                return $this->error('保存失败');
            }
            if (!empty($save)) {
                TriggerService::updateMenu();
                return $this->success('保存成功');
            }else {
                return $this->error('保存失败');
            }
        }
        $pidMenuList = $this->model->getPidMenuList();
        $this->assign(compact('id', 'pidMenuList'));
        return $this->fetch();
    }

    #[NodeAnnotation(title: '编辑', auth: true)]
    public function edit(): View|JsonResponse
    {
        $id  = request()->input('id');
        $row = $this->model->find($id);
        if (empty($row)) return $this->error('数据不存在');
        if (request()->ajax()) {
            $post      = request()->post();
            $rules     = [
                'pid'   => 'required',
                'title' => 'required',
                'icon'  => 'required',
            ];
            $validator = Validator::make($post, $rules, [
                'pid'   => '上级菜单不能为空',
                'title' => '菜单名称不能为空',
                'icon'  => '菜单图标不能为空',
            ]);
            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }
            $params = [];
            if ($row->pid == HOME_PID) $params['pid'] = HOME_PID;
            if (empty($post['href'])) $params['href'] = '';
            try {
                $save = updateFields($this->model, $row, $params);
            }catch (\Exception $e) {
                return $this->error('保存失败');
            }
            if (!empty($save)) {
                TriggerService::updateMenu();
                return $this->success('保存成功');
            }else {
                return $this->error('保存失败');
            }
        }
        $pidMenuList = $this->model->getPidMenuList();
        $this->assign(compact('id', 'row', 'pidMenuList'));
        return $this->fetch();
    }

    #[NodeAnnotation(title: '属性修改', auth: true)]
    public function modify(): JsonResponse
    {
        $post      = request()->post();
        $rules     = [
            'id'    => 'required',
            'field' => 'required',
            'value' => 'required',
        ];
        $validator = Validator::make($post, $rules, [
            'id'    => 'ID不能为空',
            'field' => '字段不能为空',
            'value' => '值不能为空',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $row = $this->model->find($post['id']);
        if (empty($row)) {
            return $this->error('数据不存在');
        }
        $homeId = $this->model->where(['pid' => HOME_PID])->value('id');
        if ($post['id'] == $homeId && $post['field'] == 'status') {
            return $this->error('首页状态不允许关闭');
        }
        try {
            foreach ($post as $key => $item) if ($key == 'field') $row->$item = $post['value'];
            $row->save();
        }catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        TriggerService::updateMenu();
        return $this->success('保存成功');
    }

    #[NodeAnnotation(title: '删除', auth: true)]
    public function delete(): JsonResponse
    {
        if (!request()->ajax()) return $this->error();
        $id = request()->input('id');
        if (!is_array($id)) $id = (array)$id;
        $row = $this->model->whereIn('id', $id)->get()->toArray();
        if (empty($row)) return $this->error('数据不存在');
        try {
            $save = $this->model->whereIn('id', $id)->delete();
        }catch (\PDOException|\Exception $e) {
            return $this->error('删除失败:' . $e->getMessage());
        }
        if ($save) {
            TriggerService::updateMenu();
            return $this->success('删除成功');
        }else {
            return $this->error('删除失败');
        }
    }

    #[NodeAnnotation(title: '添加菜单提示', auth: true)]
    public function getMenuTips(): JsonResponse
    {
        $node = request()->input('keywords');
        $list = SystemNode::where('node', 'Like', "%{$node}%")->limit(10)->select('node', 'title')->get()->toArray();
        return json([
            'code'    => 0,
            'content' => $list,
            'type'    => 'success',
        ]);
    }
}
