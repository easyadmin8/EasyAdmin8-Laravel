@include('admin.layout.head')
<div class="layuimini-container">
    <div class="layuimini-main">
        <table id="currentTable" class="layui-table layui-hide"
               data-auth-record="{:auth('system.log/record')}"
               lay-filter="currentTable">
        </table>
    </div>
</div>
@include('admin.layout.foot')
