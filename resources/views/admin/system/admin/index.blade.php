@include('admin.layout.head')
<div class="layuimini-container">
    <div class="layuimini-main">
        <div class="layui-row layui-col-space8">
            <div class="layui-col-md2 layui-hide-xs">
                <div class="layui-card-body layui-border">
                    <h2>角色列表</h2>
                    <ul class="layui-menu layui-dropdown-menu">
                        <li class="layui-menu-item-checked">
                            <div class="layui-menu-body-title" lay-on="authSearch" data-auth_id="0">全部</div>
                        </li>

                        @foreach($auth_list as $key=>$vo)
                            <li class="">
                                <div class="layui-menu-body-title" lay-on="authSearch" data-auth_id="{{$key}}">{{$vo}}</div>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
            <div class="layui-col-md10">
                <table id="currentTable" class="layui-table layui-hide"
                       data-auth-add="{{auths('system/admin/add')}}"
                       data-auth-edit="{{auths('system/admin/edit')}}"
                       data-auth-delete="{{auths('system/admin/delete')}}"
                       data-auth-password="{{auths('system/admin/password')}}"
                       lay-filter="currentTable">
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    let auth_list = JSON.parse('{!! json_encode($auth_list) !!}')
</script>

@include('admin.layout.foot')
