<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\common\AdminController;
use App\Models\SystemAdmin;
use App\Models\SystemQuick;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\View\View;

class IndexController extends AdminController
{
    public function index(): View
    {
        return $this->fetch();
    }

    /**
     * 后台首页
     * @return View
     */
    public function welcome(): View
    {
        $laravelVersion = app()::VERSION;
        $mysqlVersion   = DB::select("select VERSION() as version")[0]->version ?? '-';
        $phpVersion     = phpversion();
        $branch         = json_decode(file_get_contents(base_path() . '/composer.json'))->branch ?? 'main';
        $configIsCached = file_exists(base_path() . '/bootstrap/cache/config.php');
        $versions       = compact('laravelVersion', 'mysqlVersion', 'phpVersion', 'branch', 'configIsCached');
        $quick_list     = SystemQuick::where('status', 1)->select('id', 'title', 'icon', 'href')->orderByDesc('sort')->limit(50)->get()->toArray();
        $quicks         = array_chunk($quick_list, 8);
        return $this->fetch('', compact('quicks', 'versions'));
    }

    /**
     * 修改个人信息
     * @return View|JsonResponse
     */
    public function editAdmin(): View|JsonResponse
    {
        $id    = session('admin.id');
        $model = new SystemAdmin();
        $row   = $model->find($id);
        if (empty($row)) return $this->error(ea_trans('User information does not exist'));
        if (request()->ajax()) {
            if ($this->isDemo) return $this->error(ea_trans('Modification is not allowed in the demonstration environment', false));
            try {
                $login_type = request()->post('login_type', 1);
                if ($login_type == 2) {
                    $ga_secret = (new SystemAdmin())->where('id', $id)->value('ga_secret');
                    if (empty($ga_secret)) return $this->error(ea_trans('Please bind Google Authenticator first'));
                }
                $save = updateFields($model, $row);
            }catch (\PDOException $e) {
                return $this->error(ea_trans('operation failed', false) . ':' . $e->getMessage());
            }
            return $save ? $this->success(ea_trans('operation successful', false)) : $this->error(ea_trans('operation failed', false));
        }
        $notes = (new SystemAdmin())->notes;
        $this->assign(compact('row', 'notes'));
        return $this->fetch();
    }

    public function editPassword(): View|JsonResponse
    {
        $id    = session('admin.id');
        $model = new SystemAdmin();
        $row   = $model->find($id);
        if (empty($row)) return $this->error(ea_trans('User information does not exist'));
        if (request()->ajax()) {
            $post = request()->post();
            if ($this->isDemo) return $this->error(ea_trans('Modification is not allowed in the demonstration environment', false));
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
            $newPwd = password_hash($post['password'], PASSWORD_DEFAULT);
            if ($newPwd == $row->password) return $this->error(ea_trans('The new password cannot be the same as the old password'));
            try {
                $save = $model->where('id', $id)->update(['password' => $newPwd]);
            }catch (\Exception $e) {
                return $this->error(ea_trans('operation failed', false));
            }
            if ($save) {
                return $this->success(ea_trans('operation successful', false));
            }else {
                return $this->error(ea_trans('operation failed', false));
            }
        }
        $this->assign(compact('row'));
        return $this->fetch();
    }

    /**
     * 设置谷歌验证码
     * @param Request $request
     */
    public function set2fa(Request $request): JsonResponse|View
    {
        $id  = session('admin.id');
        $row = (new SystemAdmin())->select(['id', 'ga_secret', 'login_type'])->find($id);
        if (!$row) return $this->error(ea_trans('User information does not exist'));
        // You can see: https://gitee.com/wolf-code/authenticator
        $ga = new \Wolfcode\Authenticator\google\PHPGangstaGoogleAuthenticator();
        if (!$request->ajax()) {
            $old_secret = $row->ga_secret;
            $secret     = $ga->createSecret(32);
            $ga_title   = $this->isDemo ? 'EasyAdmin8-Laravel Demo' : 'EA-Laravel-' . Str::random(6);
            $dataUri    = $ga->getQRCode($ga_title, $secret);
            $this->assign(compact('row', 'dataUri', 'old_secret', 'secret'));
            return $this->fetch();
        }
        if ($this->isDemo) return $this->error(ea_trans('Modification is not allowed in the demonstration environment', false));
        $post      = $request->post();
        $ga_secret = $post['ga_secret'] ?? '';
        $ga_code   = $post['ga_code'] ?? '';
        if (empty($ga_code)) return $this->error(ea_trans('Please enter the verification code'));
        if (!$ga->verifyCode($ga_secret, $ga_code)) return $this->error(ea_trans('Incorrect verification code'));
        $row->ga_secret  = $ga_secret;
        $row->login_type = 2;
        $row->save();
        return $this->success(ea_trans('operation successful', false));
    }

}
