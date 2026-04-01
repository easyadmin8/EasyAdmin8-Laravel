@include('admin.layout.head')
<div class="layuimini-container"><div class="layuimini-main"><table id="currentTable" class="layui-table layui-hide"
       data-auth-add="{{auths('website/channel/add')}}"
       data-auth-edit="{{auths('website/channel/edit')}}"
       data-auth-delete="{{auths('website/channel/delete')}}"
       lay-filter="currentTable"></table></div></div>
<script>let channelTypes = JSON.parse('{!! json_encode($channelTypes,JSON_UNESCAPED_UNICODE) !!}')</script>
@include('admin.layout.foot')