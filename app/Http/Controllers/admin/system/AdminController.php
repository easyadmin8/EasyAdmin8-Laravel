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

#[ControllerAnnotation(title: 'Administrator Management')]
class AdminController extends Controller
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new SystemAdmin();
        $auth_list   = $this->model->getAuthList();
        $this->assign(compact('auth_list'));
    }

    #[NodeAnnotation(title: 'add', auth: true)]
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
                return $this->error(ea_trans('operation failed', false) . ':' . $e->getMessage());
            }
            return $save ? $this->success(ea_trans('operation successful', false)) : $this->error(ea_trans('operation failed', false));
        }
        return $this->fetch();
    }

    #[NodeAnnotation(title: 'edit', auth: true)]
    public function edit(): View|JsonResponse
    {
        $id  = request()->input('id');
        $row = $this->model->find($id);
        if (empty($row)) return $this->error(ea_trans('data does not exist', false));
        if (request()->ajax()) {
            $post               = request()->post();
            $authIds            = request()->post('auth_ids', []);
            $params['auth_ids'] = implode(',', array_keys($authIds));
            if (isset($row['password'])) unset($row['password']);
            try {
                $save = updateFields($this->model, $row, $params);
                TriggerService::updateMenu(session('admin.id'));
            }catch (\Exception $e) {
                return $this->error(ea_trans('operation failed', false) . ':' . $e->getMessage());
            }
            return $save ? $this->success(ea_trans('operation successful', false)) : $this->error(ea_trans('operation failed', false));
        }
        $row->auth_ids = explode(',', $row->auth_ids ?: '');
        $this->assign(compact('row'));
        return $this->fetch();
    }

    #[NodeAnnotation(title: 'change password', auth: true)]
    public function password(): View|JsonResponse
    {
        $id  = request()->input('id');
        $row = $this->model->find($id);
        if (empty($row)) return $this->error(ea_trans('data does not exist', false));
        if (request()->ajax()) {
            $post      = request()->post();
            $rules     = [
                'password'       => 'required',
                'password_again' => 'required',
            ];
            $validator = Validator::make($post, $rules, [
                'password'       => 'password' . ea_trans('Cannot be empty or formatted incorrectly', false),
                'password_again' => 'password_again' . ea_trans('Cannot be empty or formatted incorrectly', false),
            ]);
            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }
            if ($post['password'] != $post['password_again']) {
                return $this->error(ea_trans('passwords do not match'));
            }
            try {
                $save = $this->model->where('id', $id)->update(['password' => password_hash($post['password'], PASSWORD_DEFAULT)]);
            }catch (\Exception $e) {
                return $this->error(ea_trans('operation failed', false));
            }
            return $save ? $this->success(ea_trans('operation successful', false)) : $this->error(ea_trans('operation failed', false));
        }
        $this->assign(compact('row'));
        return $this->fetch();
    }
}
