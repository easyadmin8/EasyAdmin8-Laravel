<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ea_trans('Install EasyAdmin8 backend program',false,'','install')}}</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="static/plugs/layui-v2.x/css/layui.css?v={{time()}}" media="all">
    <link rel="stylesheet" href="static/common/css/install.css?v={{time()}}" media="all">
</head>
<body>
<h1><img src="/static/common/images/logo-1.png" alt="" style="width: 100px;height: 100px;"></h1>
<h2>{{ea_trans('Install EasyAdmin8 backend program',false,'','install')}}</h2>
<div class="content">
    <div class="lang-chose">
        <a href="{{url("install/language/zh")}}" data-lang="zh-cn" class="layui-btn layui-btn-primary layui-btn-sm">中文</a>
        <a href="{{url("install/language/en")}}" data-lang="en-us" class="layui-btn layui-btn-primary layui-btn-sm">English</a>
    </div>
    <form class="layui-form layui-form-pane" action="">
        <div class="layui-card">
            <blockquote class="layui-elem-quote layui-font-green" style="text-align: left;padding: 5px 10px;">
                <div class="layui-row">
                    <div class="layui-col-lg6 layui-col-xl6 layui-col-xs6 layui-col-sm6 layui-col-xs6">
                        {{ea_trans('website',false,'','install')}}：<a href="https://EasyAdmin8.top?spm=002.3e3c9d.29f459" target="_blank" class="layui-font-blue">EasyAdmin8.top</a>
                    </div>
                    <div class="layui-col-lg6 layui-col-xl6 layui-col-xs6 layui-col-sm6 layui-col-xs6">
                        {{ea_trans('questions',false,'','install')}}：<a href="https://EasyAdmin8.top/guide/question.html?spm=002.3e3c9d.29f460" target="_blank" class="layui-font-blue">Question</a>
                    </div>
                </div>
            </blockquote>
        </div>
        @if ($errorInfo)
            <div class="error">
                {{$errorInfo}}
            </div>
        @endif
        <div class="bg">
            <div class="layui-form-item">
                <label class="layui-form-label">{{ea_trans('Database Address',false,'','install')}}</label>
                <div class="layui-input-block">
                    <input class="layui-input" name="hostname" autocomplete="off" lay-verify="required" lay-reqtext="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Address',false,'','install')}}"
                           placeholder="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Address',false,'','install')}}" value="{{$envInfo['DB_HOST']}}">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">{{ea_trans('Database Port',false,'','install')}}</label>
                <div class="layui-input-block">
                    <input class="layui-input" name="hostport" autocomplete="off" lay-verify="required" lay-reqtext="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Port',false,'','install')}}"
                           placeholder="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Port',false,'','install')}}" value="{{$envInfo['DB_PORT']}}">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">{{ea_trans('Database Name',false,'','install')}}</label>
                <div class="layui-input-block">
                    <input class="layui-input" name="database" autocomplete="off" lay-verify="required" lay-reqtext="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Name',false,'','install')}}"
                           placeholder="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Name',false,'','install')}}" value="{{$envInfo['DB_NAME']}}">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">{{ea_trans('Database Prefix',false,'','install')}}</label>
                <div class="layui-input-block">
                    <input class="layui-input" name="prefix" autocomplete="off" lay-verify="required" lay-reqtext="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Prefix',false,'','install')}}"
                           placeholder="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Prefix',false,'','install')}}" value="{{$envInfo['DB_PREFIX']}}">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">{{ea_trans('Database Account',false,'','install')}}</label>
                <div class="layui-input-block">
                    <input class="layui-input" name="db_username" autocomplete="off" lay-verify="required" lay-reqtext="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Account',false,'','install')}}"
                           placeholder="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Account',false,'','install')}}" value="{{$envInfo['DB_USER']}}">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">{{ea_trans('Database Password',false,'','install')}}</label>
                <div class="layui-input-block">
                    <input type="password" class="layui-input" name="db_password" autocomplete="off" lay-verify="required" lay-reqtext="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Password',false,'','install')}}"
                           placeholder="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Database Password',false,'','install')}}" value="{{$envInfo['DB_PASS']}}">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">{{ea_trans('Covering the database',false,'','install')}}</label>
                <div class="layui-input-block" style="text-align: left">
                    <input type="radio" name="cover" value="1" title="{{ea_trans('cover',false,'','install')}}">
                    <input type="radio" name="cover" value="0" title="{{ea_trans('Not covered',false,'','install')}}" checked>
                </div>
            </div>
        </div>
        <div class="bg">
            <div class="layui-form-item">
                <label class="layui-form-label">{{ea_trans('Backend Address',false,'','install')}}</label>
                <div class="layui-input-block">
                    <input class="layui-input layui-disabled" id="admin_url" name="admin_url" autocomplete="off" lay-verify="required" lay-reqtext="{{ea_trans('Please Enter',false,'','install')}}" placeholder="{{ea_trans('information 2',false,'','install')}}" value="admin" readonly disabled>
                    <span class="tips">{{ea_trans('information 1',false,'','install')}}</span>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">{{ea_trans('Administrator Account',false,'','install')}}</label>
                <div class="layui-input-block">
                    <input class="layui-input" name="username" autocomplete="off" lay-verify="required" lay-reqtext="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Administrator Account',false,'','install')}}"
                           placeholder="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Administrator Password',false,'','install')}}"
                           value="admin">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">{{ea_trans('Administrator Password',false,'','install')}}</label>
                <div class="layui-input-block">
                    <input type="password" class="layui-input" name="password" maxlength="20" autocomplete="off" lay-verify="required" lay-reqtext="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Administrator Password',false,'','install')}}"
                           placeholder="{{ea_trans('Please Enter',false,'','install')}} {{ea_trans('Administrator Password',false,'','install')}}">
                </div>
            </div>
        </div>
        @csrf
        <div class="layui-form-item">
            <button class="layui-btn layui-btn-normal {{$errorInfo ? 'layui-btn-disabled' : ''}}" lay-submit="" lay-filter="install">{{ea_trans('Confirm installation',false,'','install')}}</button>
        </div>
    </form>
</div>
<script src="static/plugs/layui-v2.x/layui.js?v={{time()}}" charset="utf-8"></script>
<script>
    let isInstall = {{$isInstall?:0}}
    layui.use(['form', 'layer'], function () {
        var $ = layui.jquery,
            form = layui.form,
            layer = layui.layer;
        if (!!isInstall) {
            layer.msg("{{ea_trans('information 3',false,'','install')}}", {
                icon: 5, shade: 0.6, time: 300000,
                success: function () {
                    setTimeout(function () {
                        location.href = '/'
                    }, 5000)
                }
            })
        }
        $("#admin_url").bind("input propertychange", function () {
            var val = $(this).val();
            $("#admin_name").text(val);
        });

        form.on('submit(install)', function (data) {
            if ($(this).hasClass('layui-btn-disabled')) {
                return false;
            }
            var _data = data.field;
            var loading = layer.msg('{{ea_trans('installing',false,'','install')}}...', {
                icon: 16,
                shade: 0.2,
                time: false
            });
            $.ajax({
                url: window.location.href,
                type: 'post',
                contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                dataType: "json",
                data: _data,
                timeout: 60000,
                success: function (data) {
                    layer.close(loading);
                    if (data.code === 1) {
                        layer.msg(data.msg, {icon: 1}, function () {
                            window.location.href = '/admin';
                        });
                    } else {
                        layer.msg(data.msg, {icon: 2, time: 5000, shade: 0.3, shadeClose: true});
                    }
                },
                error: function (xhr, textstatus, thrown) {
                    layer.close(loading);
                    layer.msg('Status:' + xhr.status + '，' + xhr.statusText + '，{{ea_trans('try again later',false,'','install')}}', {icon: 2});
                    return false;
                }
            });
            return false;
        });
    });
</script>
</body>
</html>
