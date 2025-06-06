@include('admin.layout.head')
<link rel="stylesheet" href="/static/admin/css/login.css?v={{$version}}" media="all">
<div class="container">
    <div class="main-body">
        <div class="login-main">
            <div class="login-top">
                <span>{{sysconfig('site','site_name')}}</span>
                <span class="bg1"></span>
                <span class="bg2"></span>
            </div>
            <form class="layui-form login-bottom">
                <div class="demo @if(!$isDemo)layui-hide @endif">用户名:admin 密码:123456
                </div>
                <div class="center">

                    <div class="item">
                        <span class="icon icon-2"></span>
                        <input type="text" name="username" lay-verify="required" placeholder="请输入登录账号" maxlength="24"/>
                    </div>

                    <div class="item">
                        <span class="icon icon-3"></span>
                        <input type="password" name="password" lay-verify="required" placeholder="请输入密码" maxlength="20">
                        <span class="bind-password icon icon-4"></span>
                    </div>

                    <div class="item layui-hide" id="gaCode">
                        <span class="icon icon-3"></span>
                        <input type="text" name="ga_code" placeholder="谷歌验证码" maxlength="6">
                    </div>

                    @if($captcha == 1)
                        <div id="validatePanel" class="item" style="width: 137px;">
                            <input type="text" name="captcha" placeholder="请输入验证码" maxlength="4">
                            <img id="refreshCaptcha" class="validateImg" src="{{__url('login/captcha')}}" onclick="this.src='{{__url('login/captcha')}}?seed='+Math.random()">
                        </div>
                    @endif
                </div>
                <div class="tip">
                    <span class="icon-nocheck"></span>
                    <span class="login-tip">保持登录</span>
                    <a href="javascript:" class="forget-password">忘记密码？</a>
                </div>
                @csrf
                <div class="layui-form-item" style="text-align:center; width:100%;height:100%;margin:0px;">
                    <button type="button" class="login-btn" lay-submit>立即登录</button>
                </div>
            </form>
        </div>
    </div>
    <div class="footer">
        {{sysconfig('site','site_copyright')}}
        <span class="padding-5">|</span>
        <a target="_blank" href="https://beian.miit.gov.cn">
            {{sysconfig('site','site_beian')}}
        </a>
    </div>
</div>
<script>
    let backgroundUrl = "{{sysconfig('site','admin_background')}}"
</script>
@include('admin.layout.foot')
