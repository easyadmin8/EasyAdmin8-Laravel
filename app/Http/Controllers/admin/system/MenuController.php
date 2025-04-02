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

#[ControllerAnnotation(title: 'menu management')]
class MenuController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new SystemMenu();
    }

    #[NodeAnnotation(title: 'add', auth: true)]
    public function add(): View|JsonResponse
    {
        $id     = request()->input('id');
        $homeId = $this->model->where(['pid' => HOME_PID,])->value('id');
        if ($id == $homeId) {
            return $this->error(ea_trans('Homepage cannot add submenus'));
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
                'pid'    => 'pid' . ea_trans('Cannot be empty', false),
                'title'  => 'title' . ea_trans('Cannot be empty', false),
                'icon'   => 'icon' . ea_trans('Cannot be empty', false),
                'target' => 'target' . ea_trans('Cannot be empty', false),
            ]);
            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }
            $params = [];
            if (empty($post['href'])) $params['href'] = '';
            try {
                $save = insertFields($this->model, $params);
            }catch (\Exception $e) {
                return $this->error(ea_trans('operation failed', false));
            }
            if (!empty($save)) {
                TriggerService::updateMenu();
                return $this->success(ea_trans('operation successful', false));
            }else {
                return $this->error(ea_trans('operation failed', false));
            }
        }
        $pidMenuList = $this->model->getPidMenuList();
        $this->assign(compact('id', 'pidMenuList'));
        return $this->fetch();
    }

    #[NodeAnnotation(title: 'edit', auth: true)]
    public function edit(): View|JsonResponse
    {
        $id  = request()->input('id');
        $row = $this->model->find($id);
        if (empty($row)) return $this->error(ea_trans('data does not exist', false));
        if (request()->ajax()) {
            $post      = request()->post();
            $rules     = [
                'pid'   => 'required',
                'title' => 'required',
                'icon'  => 'required',
            ];
            $validator = Validator::make($post, $rules, [
                'pid'   => 'pid' . ea_trans('Cannot be empty', false),
                'title' => 'title' . ea_trans('Cannot be empty', false),
                'icon'  => 'icon' . ea_trans('Cannot be empty', false),
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
                return $this->error(ea_trans('operation failed', false));
            }
            if (!empty($save)) {
                TriggerService::updateMenu();
                return $this->success(ea_trans('operation successful', false));
            }else {
                return $this->error(ea_trans('operation failed', false));
            }
        }
        $pidMenuList = $this->model->getPidMenuList();
        $this->assign(compact('id', 'row', 'pidMenuList'));
        return $this->fetch();
    }

    #[NodeAnnotation(title: 'modify', auth: true)]
    public function modify(): JsonResponse
    {
        $post      = request()->post();
        $rules     = [
            'id'    => 'required',
            'field' => 'required',
            'value' => 'required',
        ];
        $validator = Validator::make($post, $rules, [
            'id'    => 'id' . ea_trans('Cannot be empty', false),
            'field' => 'field' . ea_trans('Cannot be empty', false),
            'value' => 'value' . ea_trans('Cannot be empty', false),
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $row = $this->model->find($post['id']);
        if (empty($row)) {
            return $this->error(ea_trans('data does not exist', false));
        }
        $homeId = $this->model->where(['pid' => HOME_PID])->value('id');
        if ($post['id'] == $homeId && $post['field'] == 'status') {
            return $this->error(ea_trans('Homepage status does not allow closing'));
        }
        try {
            foreach ($post as $key => $item) if ($key == 'field') $row->$item = $post['value'];
            $row->save();
        }catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        TriggerService::updateMenu();
        return $this->success(ea_trans('operation successful', false));
    }

    #[NodeAnnotation(title: 'delete', auth: true)]
    public function delete(): JsonResponse
    {
        if (!request()->ajax()) return $this->error();
        $id = request()->input('id');
        if (!is_array($id)) $id = (array)$id;
        $row = $this->model->whereIn('id', $id)->get()->toArray();
        if (empty($row)) return $this->error(ea_trans('data does not exist', false));
        try {
            $save = $this->model->whereIn('id', $id)->delete();
        }catch (\PDOException|\Exception $e) {
            return $this->error(ea_trans('operation failed', false) . ':' . $e->getMessage());
        }
        if ($save) {
            TriggerService::updateMenu();
            return $this->success(ea_trans('operation successful', false));
        }else {
            return $this->error(ea_trans('operation failed', false));
        }
    }

    #[NodeAnnotation(title: 'Add menu prompt', auth: true)]
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
