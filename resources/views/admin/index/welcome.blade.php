@include('admin.layout.head')
<link rel="stylesheet" href="/static/admin/css/welcome.css?v={{$version}}" media="all">
<div class="layui-layout layui-padding-2">
    <div class="layui-layout-admin">
        <div class="layui-row layui-col-space10">
            <div class="layui-col-md8 ">
                <div class="layui-row layui-col-space10">
                    <div class="layui-col-md6 ">
                        <div class="layui-card">
                            <div class="layui-card-header"><i class="fa fa-warning icon"></i>数据统计</div>
                            <div class="layui-card-body">
                                <div class="welcome-module">
                                    <div class="layui-row layui-col-space10">
                                        <div class="layui-col-xs6">
                                            <div class="layui-panel">
                                                <div class="layui-card-body">
                                                    <span class="layui-badge layui-bg-cyan fa-pull-right ">实时</span>
                                                    <div class="panel-content">
                                                        <h5>用户统计</h5>
                                                        <h2>1234</h2>
                                                        <h6>当前分类总记录数</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-col-xs6">
                                            <div class="layui-panel">
                                                <div class="layui-card-body">
                                                    <span class="layui-badge layui-bg-purple fa-pull-right ">实时</span>
                                                    <div class="panel-content">
                                                        <h5>商品统计</h5>
                                                        <h2>1234</h2>
                                                        <h6>当前分类总记录数</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-col-xs6">
                                            <div class="layui-panel">
                                                <div class="layui-card-body ">
                                                    <span class="layui-badge layui-bg-orange fa-pull-right ">实时</span>
                                                    <div class="panel-content">
                                                        <h5>浏览统计</h5>
                                                        <h2>1234</h2>
                                                        <h6>当前分类总记录数</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-col-xs6">
                                            <div class="layui-panel">
                                                <div class="layui-card-body ">
                                                    <span class="layui-badge layui-bg-red fa-pull-right ">实时</span>
                                                    <div class="panel-content">
                                                        <h5>订单统计</h5>
                                                        <h2>1234</h2>
                                                        <h6>当前分类总记录数</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md6 ">
                        <div class="layui-card">
                            <div class="layui-card-header"><i class="fa fa-credit-card icon icon-blue"></i>快捷入口</div>
                            <div class="layui-card-body">
                                <div class="welcome-module">
                                    <div class="layui-row layui-col-space10">

                                        <div class="swiper mySwiper">
                                            <div class="swiper-wrapper">
                                                @foreach($quicks as $value)

                                                    <div class="swiper-slide">
                                                        @foreach($value as $vo)

                                                            <div class="layui-col-xs3 layuimini-qiuck-module">
                                                                <a layuimini-content-href="{{__url($vo['href'])}}" data-title="{{$vo['title']}}">
                                                                    <i class="{{$vo['icon']}}"></i>
                                                                    <cite>{{$vo['title']}}</cite>
                                                                </a>
                                                            </div>
                                                        @endforeach

                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="layui-col-md12 ">
                        <div class="layui-card">
                            <div class="layui-card-header"><i class="fa fa-line-chart icon"></i>报表统计</div>
                            <div class="layui-card-body">
                                <div id="echarts-records" style="width: 100%;min-height:500px"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-col-md4 ">

                <div class="layui-card">
                    <div class="layui-card-header"><i class="fa fa-fire icon"></i>版本信息</div>
                    <div class="layui-card-body layui-text">
                        <table class="layui-table">
                            <colgroup>
                                <col width="150">
                                <col>
                            </colgroup>
                            <tbody>
                            <tr>
                                <td>框架名称</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">EasyAdmin8-Laravel</button>
                                </td>
                            </tr>
                            <tr>
                                <td>分支版本</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">{{$versions['branch']??"main"}}</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Laravel版本</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">{{$versions['laravelVersion']??''}}</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Config配置缓存</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">{{$versions['configIsCached']?'已开启':'未开启'}}</button>
                                </td>
                            </tr>
                            <tr>
                                <td>PHP版本</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">{{$versions['phpVersion']??''}}</button>
                                </td>
                            </tr>
                            <tr>
                                <td>MySQL版本</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">{{$versions['mysqlVersion']??''}}</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Layui版本</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary" id="layui-version">-</button>
                                </td>
                            </tr>
                            <tr>
                                <td>主要特色</td>
                                <td>
                                    <span class="layui-btn layui-btn-xs layui-btn-primary layui-border">零门槛</span>
                                    <span class="layui-btn layui-btn-xs layui-btn-primary layui-border">响应式</span>
                                    <span class="layui-btn layui-btn-xs layui-btn-primary layui-border">清爽</span>
                                    <span class="layui-btn layui-btn-xs layui-btn-primary layui-border">极简</span>
                                </td>
                            </tr>
                            <tr>
                                <td>composer信息</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-bg-cyan" lay-on="showComposerInfo">点击查看</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Gitee</td>
                                <td>
                                    <div class="layui-btn-container">
                                        <a href='https://gitee.com/wolf18/EasyAdmin8-Laravel' target="_blank">
                                            <img src='https://gitee.com/wolf18/EasyAdmin8-Laravel/badge/star.svg?theme=dark' alt='star'/>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Github</td>
                                <td>
                                    <a href="https://github.com/wolf-leo/EasyAdmin8-Laravel" target="_blank" style="text-decoration: none;">
                                        <i class="layui-icon layui-icon-github" style="font-size: 25px; color: #333333;"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>其他版本</td>
                                <td>
                                    <a href="http://laravel-i18n.easyadmin8.top/" target="_blank" class="layui-text-em">
                                        <button type="button" class="layui-btn layui-bg-blue layui-btn-xs layui-btn-radius">
                                            <i class="layui-icon layui-icon-website" style="font-size: 15px;"></i>多语言版
                                        </button>
                                    </a>
                                    <a href="http://laravel-10.easyadmin8.top/" target="_blank" class="layui-text-em">
                                        <button type="button" class="layui-btn layui-bg-blue layui-btn-xs layui-btn-radius">
                                            <i class="layui-icon layui-icon-website" style="font-size: 15px;"></i>10.x版
                                        </button>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="layui-card">
                    <div class="layui-card-header"><i class="fa fa-paper-plane-o icon"></i>作者心语</div>
                    <div class="layui-card-body layui-text">
                        <p class="layui-font-cyan">
                            本模板基于layui2.11.x以及font-awesome-6.x进行实现。
                            <a class="layui-btn layui-btn-xs layui-btn-danger" style="vertical-align: baseline;" target="_blank" href="http://layui.dev/docs">layui文档</a>
                        </p>
                        <hr>
                        <p class="layui-font-red">备注：此后台框架永久开源，但请勿进行出售或者上传到任何素材网站，否则将追究相应的责任。</p>
                        <hr>
                        <div class="layui-card-header"><i class="fa fa-qq icon"></i>QQ交流群</div>
                        <div class="layui-card-body">
                            <img src="/static/common/images/EasyAdmin8-Laravel.png" width="145">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@include('admin.layout.foot')
