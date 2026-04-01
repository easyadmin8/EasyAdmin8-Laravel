@include('admin.layout.head')
<div class="layuimini-container"><div class="layuimini-main"><table id="currentTable" class="layui-table layui-hide"
       data-auth-add="{{auths('website/article/add')}}"
       data-auth-edit="{{auths('website/article/edit')}}"
       data-auth-delete="{{auths('website/article/delete')}}"
       lay-filter="currentTable"></table></div></div>
<script>let categories = JSON.parse('{!! json_encode($categories,JSON_UNESCAPED_UNICODE) !!}')</script>
@include('admin.layout.foot')