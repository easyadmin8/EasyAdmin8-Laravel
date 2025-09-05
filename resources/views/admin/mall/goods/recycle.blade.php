@include('admin.layout.head')
<div class="layuimini-container">
    <div class="layuimini-main">
        <table id="currentTable" class="layui-table layui-hide"
               data-auth-recycle="{{auths('mall/goods/recycle')}}"
               lay-filter="currentTable">
        </table>
    </div>
</div>
<script>
    let cate = JSON.parse('{!! json_encode($cate,JSON_UNESCAPED_UNICODE) !!}')
</script>
@include('admin.layout.foot')
