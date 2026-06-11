@include('admin.layout.head')
<script src="/static/common/js/zone.js?v={{$version}}"></script>
<div class="layuimini-container">
    <div class="layuimini-main">
        <table id="currentTable" class="layui-table layui-hide"
               data-auth-add="{{auths('mall/goods/add')}}"
               data-auth-edit="{{auths('mall/goods/edit')}}"
               data-auth-delete="{{auths('mall/goods/delete')}}"
               data-auth-stock="{{auths('mall/goods/stock')}}"
               data-auth-recycle="{{auths('mall/goods/recycle')}}"
               lay-filter="currentTable">
        </table>
    </div>
</div>
<script>
    let cateSelects = JSON.parse('{!! json_encode($cate,JSON_UNESCAPED_UNICODE) !!}')
</script>

<script type="text/html" id="provinceDemo">
    <div class="layui-col-xs4">
        <select id="province" name="province" lay-filter="province"></select>
    </div>
</script>

<script type="text/html" id="cityDemo">
    <div class="layui-col-xs4">
        <select id="city" name="city" lay-filter="city"></select>
    </div>
</script>

<script type="text/html" id="areaDemo">
    <div class="layui-col-xs4">
        <select id="area" name="province" lay-filter="area"></select>
    </div>
</script>
@include('admin.layout.foot')
