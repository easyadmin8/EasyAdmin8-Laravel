<?php

namespace App\Http\Controllers\admin;

use App\Helpers\Utils;
use App\Http\Controllers\common\AdminController;
use App\Models\SystemAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Webman\Captcha\CaptchaBuilder;
use Webman\Captcha\PhraseBuilder;
use Wolfcode\RateLimiting\Attributes\RateLimitingMiddleware;

class LoginController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        if (\request()->method() == 'GET' && !empty(session('admin')) && $this->action != 'out') {
            $adminModuleName = $this->adminConfig['admin_alias_name'];
            redirect(__url())->send();
        }
    }

    #[RateLimitingMiddleware(key: [Utils::class, 'getIp'], seconds: 1, limit: 1, message: '操作过于频繁，请稍后再试')]
    public function index(): View|JsonResponse
    {
        $captcha = config('easyadmin.CAPTCHA', false);
        if (!request()->ajax()) {
            return view('admin.login', compact('captcha'));
        }
        if ($captcha) {
            if (strtolower(request()->post('captcha')) !== request()->session()->get('captcha')) {
                return $this->error('图片验证码错误');
            }
        }
        $post      = \request()->post();
        $rules     = [
            'username'   => 'required',
            'password'   => 'required',
            'keep_login' => 'required',
        ];
        $validator = Validator::make($post, $rules, [
            'username' => '用户名不能为空',
            'password' => '密码不能为空或格式错误',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $admin = SystemAdmin::where(['username' => $post['username']])->first();
        if (empty($admin) || !password_verify($post['password'], $admin->password)) {
            return $this->error('用户名或密码有误');
        }
        if ($admin->status == 0) {
            return $this->error('账号已被禁用');
        }
        if ($admin->login_type == 2) {
            if (empty($post['ga_code'])) return $this->error('请输入谷歌验证码', ['is_ga_code' => true]);
            $ga = new \Wolfcode\Authenticator\google\PHPGangstaGoogleAuthenticator();
            if (!$ga->verifyCode($admin->ga_secret, $post['ga_code'])) return $this->error('谷歌验证码错误');;
        }
        $admin->login_num   += 1;
        $admin->update_time = time();
        $admin->save();
        $admin = $admin->toArray();
        unset($admin['password']);
        $admin['expire_time'] = $post['keep_login'] == 1 ? true : time() + 7200;
        session(compact('admin'));
        return $this->success('登录成功', [], __url());
    }

    public function captcha(): Response
    {
        $length  = 4;
        $chars   = '0123456789';
        $phrase  = new PhraseBuilder($length, $chars);
        $builder = new CaptchaBuilder(null, $phrase);
        $builder->build();
        session()->put('captcha', strtolower($builder->getPhrase()));
        $img_content = $builder->get();
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);

    }

    public function out(): Response|JsonResponse
    {
        \request()->session()->forget('admin');
        return $this->success('退出登录成功', [], __url('/login'));
    }
}
