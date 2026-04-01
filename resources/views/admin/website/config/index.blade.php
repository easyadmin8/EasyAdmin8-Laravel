@include('admin.layout.head')
<div class="layuimini-container">
    <div class="layuimini-main" id="app">
        <div class="layui-tab layui-tab-brief" lay-filter="websiteConfigTab">
            <ul class="layui-tab-title">
                <li class="layui-this" data-group="website">站点配置</li>
                <li data-group="website_footer">页脚 / 联系方式</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">@include('admin.website.config.site')</div>
                <div class="layui-tab-item">@include('admin.website.config.footer')</div>
            </div>
        </div>
    </div>
</div>
@include('admin.layout.foot')
