@include('admin.layout.head')
<div class="layuimini-container"><div class="layuimini-main"><table id="currentTable" class="layui-table layui-hide"
       data-auth-add="{{auths('website/product_category/add')}}"
       data-auth-edit="{{auths('website/product_category/edit')}}"
       data-auth-delete="{{auths('website/product_category/delete')}}"
       lay-filter="currentTable"></table></div></div>
@include('admin.layout.foot')