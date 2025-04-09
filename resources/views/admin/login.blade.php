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
                <div class="demo @if(!$isDemo)layui-hide@endif">{{ea_trans('username',false)}}:admin {{ea_trans('password',false)}}:123456</div>
                <div class="center">

                    <div class="item">
                        <span class="icon icon-2"></span>
                        <input type="text" name="username" lay-verify="required" placeholder="{{ea_trans('username placeholder')}}" maxlength="24"/>
                    </div>

                    <div class="item">
                        <span class="icon icon-3"></span>
                        <input type="password" name="password" lay-verify="required" placeholder="{{ea_trans('password placeholder')}}" maxlength="20">
                        <span class="bind-password icon icon-4"></span>
                    </div>

                    <div class="item layui-hide" id="gaCode">
                        <span class="icon icon-3"></span>
                        <input type="text" name="ga_code" placeholder="{{ea_trans('ga placeholder')}}" maxlength="6">
                    </div>

                    @if($captcha == 1)
                        <div id="validatePanel" class="item" style="width: 137px;">
                            <input type="text" name="captcha" placeholder="{{ea_trans('code placeholder')}}" maxlength="4">
                            <img id="refreshCaptcha" class="validateImg" src="{{__url('login/captcha')}}" onclick="this.src='{{__url('login/captcha')}}?seed='+Math.random()">
                        </div>
                    @endif
                </div>
                <div class="tip">
                    <span class="icon-nocheck"></span>
                    <span class="login-tip">{{ea_trans('keep login')}}</span>
                    <a href="javascript:" class="forget-password">{{ea_trans('forgot password')}}?</a>
                </div>
                @csrf
                <div class="layui-form-item" style="text-align:center; width:100%;height:100%;margin:0px;">
                    <button type="button" class="login-btn" lay-submit>{{ea_trans('login now')}}</button>
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
