<?php

namespace App\Http\Controllers\admin\system;

use App\Http\Controllers\common\AdminController as Controller;
use App\Http\Services\annotation\NodeAnnotation;
use App\Http\Services\annotation\ControllerAnnotation;
use App\Http\Services\TriggerService;
use App\Models\SystemAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

#[ControllerAnnotation(title: '管理员管理')]
class AdminController extends Controller
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new SystemAdmin();
        $auth_list   = $this->model->getAuthList();
        $this->assign(compact('auth_list'));
    }

    #[NodeAnnotation(title: '添加', auth: true)]
    public function add(): View|JsonResponse
    {
        if (request()->ajax()) {
            $post               = request()->post();
            $authIds            = request()->post('auth_ids', []);
            $params['auth_ids'] = implode(',', array_keys($authIds));
            if (empty($post['password'])) $post['password'] = '123456';
            $params['password'] = password($post['password']);
            try {
                $save = insertFields($this->model, $params);
            }catch (\Exception $e) {
                return $this->error('保存失败:' . $e->getMessage());
            }
            return $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        return $this->fetch();
    }

    #[NodeAnnotation(title: '编辑', auth: true)]
    public function edit(): View|JsonResponse
    {
        $id  = request()->input('id');
        $row = $this->model->find($id);
        if (empty($row)) return $this->error('数据不存在');
        if (request()->ajax()) {
            $post               = request()->post();
            $authIds            = request()->post('auth_ids', []);
            $params['auth_ids'] = implode(',', array_keys($authIds));
            if (isset($row['password'])) unset($row['password']);
            try {
                $save = updateFields($this->model, $row, $params);
                TriggerService::updateMenu(session('admin.id'));
            }catch (\Exception $e) {
                return $this->error('保存失败:' . $e->getMessage());
            }
            return $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        $row->auth_ids = explode(',', $row->auth_ids ?: '');
        $this->assign(compact('row'));
        return $this->fetch();
    }

    #[NodeAnnotation(title: '修改密码', auth: true)]
    public function password(): View|JsonResponse
    {
        $id  = request()->input('id');
        $row = $this->model->find($id);
        if (empty($row)) return $this->error('数据不存在');
        if (request()->ajax()) {
            $post      = request()->post();
            $rules     = [
                'password'       => 'required',
                'password_again' => 'required',
            ];
            $validator = Validator::make($post, $rules, [
                'password'       => '密码不能为空或格式错误',
                'password_again' => '确认密码不能为空或格式错误',
            ]);
            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }
            if ($post['password'] != $post['password_again']) {
                return $this->error('两次密码输入不一致');
            }
            try {
                $save = $this->model->where('id', $id)->update(['password' => password_hash($post['password'], PASSWORD_DEFAULT)]);
            }catch (\Exception $e) {
                return $this->error('保存失败');
            }
            return $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        $this->assign(compact('row'));
        return $this->fetch();
    }
}
