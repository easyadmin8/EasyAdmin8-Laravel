@include('admin.layout.head')
<div class="layuimini-container"><div class="layuimini-main"><table id="currentTable" class="layui-table layui-hide"
       data-auth-add="{{auths('website/hot_keyword/add')}}"
       data-auth-edit="{{auths('website/hot_keyword/edit')}}"
       data-auth-delete="{{auths('website/hot_keyword/delete')}}"
       lay-filter="currentTable"></table></div></div>
@include('admin.layout.foot')