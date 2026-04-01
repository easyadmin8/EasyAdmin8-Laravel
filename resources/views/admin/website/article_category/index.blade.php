@include('admin.layout.head')
<div class="layuimini-container"><div class="layuimini-main"><table id="currentTable" class="layui-table layui-hide"
       data-auth-add="{{auths('website/article_category/add')}}"
       data-auth-edit="{{auths('website/article_category/edit')}}"
       data-auth-delete="{{auths('website/article_category/delete')}}"
       lay-filter="currentTable"></table></div></div>
@include('admin.layout.foot')