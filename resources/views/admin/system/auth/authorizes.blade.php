@include('admin.layout.head')

<link rel="stylesheet" href="/static/plugs/zTree/fontawesome.css">
<link rel="stylesheet" href="/static/plugs/zTree/zTreeStyle.css?v={{$version}}">
<script src='/static/plugs/jquery-3.4.1/jquery-3.4.1.min.js'></script>
<script src='/static/plugs/zTree/jquery.ztree.core.js'></script>
<script src='/static/plugs/zTree/jquery.ztree.excheck.js'></script>

<div class="layuimini-container">
    <form id="app-form" class="layui-form layuimini-form">

        <div class="layui-form-item">
            <label class="layui-form-label required">权限名称</label>
            <div class="layui-input-block">
                <input type="text" name="title" readonly class="layui-input" value="{{$row['title']}}">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">分配节点</label>
            <div class="layui-input-block">
                <ul id="tree" class="ztree"></ul>
            </div>
        </div>

        <input type="hidden" name="id" readonly class="layui-input" value="{{$row['id']}}">

        <div class="hr-line"></div>
        <div class="layui-form-item text-center">
            <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm" lay-submit="system.auth/saveAuthorize">确认</button>
            <button type="reset" class="layui-btn layui-btn-primary layui-btn-sm">重置</button>
        </div>

    </form>
</div>
@include('admin.layout.foot')
