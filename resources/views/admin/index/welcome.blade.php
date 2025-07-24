@include('admin.layout.head')
<link rel="stylesheet" href="/static/admin/css/welcome.css?v={{$version}}" media="all">
<div class="layui-layout layui-padding-2">
    <div class="layui-layout-admin">
        <div class="layui-row layui-col-space10">
            <div class="layui-col-md8 ">
                <div class="layui-row layui-col-space10">
                    <div class="layui-col-md6 ">
                        <div class="layui-card">
                            <div class="layui-card-header"><i class="fa fa-warning icon"></i>{{ ea_trans('data statistics') }}</div>
                            <div class="layui-card-body">
                                <div class="welcome-module">
                                    <div class="layui-row layui-col-space10">
                                        <div class="layui-col-xs6">
                                            <div class="layui-panel">
                                                <div class="layui-card-body">
                                                    <span class="layui-badge layui-bg-cyan fa-pull-right ">{{ea_trans('real time')}}</span>
                                                    <div class="panel-content">
                                                        <h5>{{ ea_trans('user statistics') }}</h5>
                                                        <h2>1234</h2>
                                                        <h6>{{ ea_trans('total number') }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-col-xs6">
                                            <div class="layui-panel">
                                                <div class="layui-card-body">
                                                    <span class="layui-badge layui-bg-purple fa-pull-right ">{{ea_trans('real time')}}</span>
                                                    <div class="panel-content">
                                                        <h5>{{ ea_trans('product statistics') }}</h5>
                                                        <h2>1234</h2>
                                                        <h6>{{ ea_trans('total number') }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-col-xs6">
                                            <div class="layui-panel">
                                                <div class="layui-card-body ">
                                                    <span class="layui-badge layui-bg-orange fa-pull-right ">{{ea_trans('real time')}}</span>
                                                    <div class="panel-content">
                                                        <h5>{{ ea_trans('browse statistics') }}</h5>
                                                        <h2>1234</h2>
                                                        <h6>{{ ea_trans('total number') }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="layui-col-xs6">
                                            <div class="layui-panel">
                                                <div class="layui-card-body ">
                                                    <span class="layui-badge layui-bg-red fa-pull-right ">{{ea_trans('real time')}}</span>
                                                    <div class="panel-content">
                                                        <h5>{{ ea_trans('order statistics') }}</h5>
                                                        <h2>1234</h2>
                                                        <h6>{{ ea_trans('total number') }}</h6>
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
                            <div class="layui-card-header"><i class="fa fa-credit-card icon icon-blue"></i>{{ea_trans('quick entrance')}}</div>
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
                                                                    <cite>{{ea_trans($vo['title'],false,'','admin.js')}}</cite>
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
                            <div class="layui-card-header"><i class="fa fa-line-chart icon"></i>{{ea_trans('report statistics')}}</div>
                            <div class="layui-card-body">
                                <div id="echarts-records" style="width: 100%;min-height:500px"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-col-md4 ">

                <div class="layui-card">
                    <div class="layui-card-header"><i class="fa fa-fire icon"></i>{{ea_trans('version information')}}</div>
                    <div class="layui-card-body layui-text">
                        <table class="layui-table">
                            <colgroup>
                                <col width="150">
                                <col>
                            </colgroup>
                            <tbody>
                            <tr>
                                <td>{{ea_trans('frame name')}}</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">EasyAdmin8-Laravel</button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ea_trans('branch version')}}</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">{{$versions['branch']??"main"}}</button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ea_trans('laravel version')}}</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">{{$versions['laravelVersion']??''}}</button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ea_trans('config configuration cache')}}</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">{{$versions['configIsCached']?ea_trans('Opened'):ea_trans('Unopened')}}</button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ea_trans('php version')}}</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">{{$versions['phpVersion']??''}}</button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ea_trans('mysql version')}}</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary">{{$versions['mysqlVersion']??''}}</button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ea_trans('layui version')}}</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-primary" id="layui-version">-</button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ea_trans('main features')}}</td>
                                <td>
                                    <span class="layui-btn layui-btn-xs layui-btn-primary layui-border">{{ea_trans('zero threshold')}}</span>
                                    <span class="layui-btn layui-btn-xs layui-btn-primary layui-border">{{ea_trans('responsive')}}</span>
                                    <span class="layui-btn layui-btn-xs layui-btn-primary layui-border">{{ea_trans('refreshing')}}</span>
                                    <span class="layui-btn layui-btn-xs layui-btn-primary layui-border">{{ea_trans('minimalism')}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>composer info</td>
                                <td>
                                    <button type="button" class="layui-btn layui-btn-xs layui-bg-cyan" lay-on="showComposerInfo">detail</button>
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
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="layui-card">
                    <div class="layui-card-header"><i class="fa fa-paper-plane-o icon"></i>{{ea_trans('author suggestion')}}</div>
                    <div class="layui-card-body layui-text">
                        <p class="layui-font-cyan">
                            {{ea_trans('template_message1')}}
                            <a class="layui-btn layui-btn-xs layui-btn-danger" style="vertical-align: baseline;" target="_blank" href="http://layui.dev/docs">{{ea_trans('layui document')}}</a>
                        </p>
                        <hr>
                        <p class="layui-font-red">{{ea_trans('template_message2')}}</p>
                        <hr>
                        <div class="layui-card-header"><i class="fa fa-qq icon"></i>{{ea_trans('QQ communication group')}}</div>
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
