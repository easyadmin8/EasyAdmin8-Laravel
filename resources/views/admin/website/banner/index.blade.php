@include('admin.layout.head')
<div class="layuimini-container"><div class="layuimini-main"><table id="currentTable" class="layui-table layui-hide"
       data-auth-add="{{auths('website/banner/add')}}"
       data-auth-edit="{{auths('website/banner/edit')}}"
       data-auth-delete="{{auths('website/banner/delete')}}"
       lay-filter="currentTable"></table></div></div>
@include('admin.layout.foot')