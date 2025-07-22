@include('admin.layout.head')
<link rel="stylesheet" href="/static/plugs/lay-module/layuimini/layuimini.css?v={{$version}}" media="all">
<link rel="stylesheet" href="/static/plugs/lay-module/layuimini/themes/default.css?v={{$version}}" media="all">
<style id="layuimini-bg-color"></style>
<div class="layui-layout-body layuimini-all">
    <div class="layui-layout layui-layout-admin">

        <div class="layui-header header">
            <div class="layui-logo layuimini-logo"></div>

            <div class="layuimini-header-content">
                <a>
                    <div class="layuimini-tool"><i class="fa fa-outdent" data-side-fold="1"></i></div>
                </a>

                <!--电脑端头部菜单-->
                <ul class="layui-nav layui-layout-left layuimini-header-menu layuimini-menu-header-pc layuimini-pc-show">
                </ul>

                <!--手机端头部菜单-->
                <ul class="layui-nav layui-layout-left layuimini-header-menu layuimini-mobile-show">
                    <li class="layui-nav-item">
                        <a href="javascript:;"><i class="fa fa-list-ul"></i> {{ea_trans('select module',false)}}</a>
                        <dl class="layui-nav-child layuimini-menu-header-mobile">
                        </dl>
                    </li>
                </ul>

                <ul class="layui-nav layui-layout-right">
                    <!--                    <li class="layui-nav-item" lay-unselect>-->
                    <!--                        <div class="layui-form ws-header-theme" lay-filter="header-theme">-->
                    <!--                            <input type="checkbox" name="theme-mode" id="ID-header-theme-mode" lay-filter="header-theme-mode" lay-skin="switch">-->
                    <!--                            <div lay-checkbox>-->
                    <!--                                <i class="layui-icon layui-icon-moon"></i> |-->
                    <!--                                <i class="layui-icon layui-icon-light"></i>-->
                    <!--                            </div>-->
                    <!--                        </div>-->
                    <!--                    </li>-->
                    <li class="layui-nav-item" lay-unselect>
                        <a href="http://easyadmin8.top" target="_blank"><i class="fa fa-home"></i></a>
                    </li>
                    <li class="layui-nav-item" lay-unselect>
                        <a href="javascript:;" data-refresh="{{ea_trans('refresh',false)}}"><i class="fa fa-refresh"></i></a>
                    </li>
                    <li class="layui-nav-item" lay-unselect>
                        <a href="javascript:;" data-clear="{{ea_trans('clean',false)}}" class="layuimini-clear"><i class="fa fa-trash"></i></a>
                    </li>
                    <li class="layui-nav-item mobile layui-hide-xs" lay-unselect>
                        <a href="javascript:;" data-check-screen="full"><i class="fa fa-arrows-alt"></i></a>
                    </li>
                    <li class="layui-nav-item layuimini-setting">
                        <a href="javascript:;">
                            {{config('admin.locale')[session('locale',config('app.locale'))]}}
                        </a>
                        <dl class="layui-nav-child">
                            @foreach(config('admin.locale') as $key=>$value)
                                <dd>
                                    <a href='{{__url("language/$key")}}'><i class="fa fa-globe"></i> {{$value}}</a>
                                </dd>
                            @endforeach
                        </dl>
                    </li>
                    <li class="layui-nav-item layuimini-setting">
                        <a href="javascript:;">
                            <img src="{{session('admin.head_img')}}" class="layui-nav-img" width="50" height="50">
                            <cite class="adminName">{{session('admin.username')}}</cite>
                            <span class="layui-nav-more"></span>
                        </a>
                        <dl class="layui-nav-child">
                            <dd>
                                <a href="javascript:;" layuimini-content-href="{{__url('index/editAdmin')}}" data-title="{{ea_trans('basic information')}}" data-icon="fa fa-gears">{{ea_trans('basic information')}}<span class="layui-badge-dot"></span></a>
                            </dd>
                            <dd>
                                <a href="javascript:;" layuimini-content-href="{{__url('index/editPassword')}}" data-title="{{ea_trans('change password')}}" data-icon="fa fa-gears">{{ea_trans('change password')}}</a>
                            </dd>
                            <dd>
                                <hr>
                            </dd>
                            <dd>
                                <a href="javascript:;" class="login-out">{{ea_trans('logout')}}</a>
                            </dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item layuimini-select-bgcolor" lay-unselect>
                        <a href="javascript:;" data-bgcolor="{{ea_trans('color scheme')}}"><i class="fa fa-ellipsis-v"></i></a>
                    </li>
                </ul>
            </div>
        </div>

        <!--无限极左侧菜单-->
        <div class="layui-side layui-bg-black layuimini-menu-left">
        </div>

        <!--初始化加载层-->
        <div class="layuimini-loader">
            <div class="layuimini-loader-inner"></div>
        </div>

        <!--手机端遮罩层-->
        <div class="layuimini-make"></div>

        <!-- 移动导航 -->
        <div class="layuimini-site-mobile"><i class="layui-icon"></i></div>

        <div class="layui-body">
            <div class="layuimini-tab layui-tab-rollTool layui-tab" lay-filter="layuiminiTab" lay-allowclose="true">
                <ul class="layui-tab-title">
                    <li class="layui-this" id="layuiminiHomeTabId" lay-id=""></li>
                </ul>
                <div class="layui-tab-control">
                    <li class="layuimini-tab-roll-left layui-icon layui-icon-left"></li>
                    <li class="layuimini-tab-roll-right layui-icon layui-icon-right"></li>
                    <li class="layui-tab-tool layui-icon layui-icon-down">
                        <ul class="layui-nav close-box">
                            <li class="layui-nav-item">
                                <a href="javascript:;"><span class="layui-nav-more"></span></a>
                                <dl class="layui-nav-child">
                                    <dd><a href="javascript:;" layuimini-tab-close="current">{{ea_trans('close current')}}</a></dd>
                                    <dd><a href="javascript:;" layuimini-tab-close="other">{{ea_trans('close else')}}</a></dd>
                                    <dd><a href="javascript:;" layuimini-tab-close="all">{{ea_trans('close all')}}</a></dd>
                                </dl>
                            </li>
                        </ul>
                    </li>
                </div>
                <div class="layui-tab-content">
                    <div id="layuiminiHomeTabIframe" class="layui-tab-item layui-show"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.layout.foot')
