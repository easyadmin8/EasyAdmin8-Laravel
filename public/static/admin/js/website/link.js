define(["jquery", "easy-admin"], function ($, ea) {
    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'website/link/index',
        add_url: 'website/link/add',
        edit_url: 'website/link/edit',
        delete_url: 'website/link/delete',
        export_url: 'website/link/export',
        modify_url: 'website/link/modify',
    };
    return {
        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: "checkbox"},
                    {field:'id', width:80, title:'ID', searchOp:'='},
                    {field:'sort', width:80, title:'排序', edit:'text'},
                    {field:'title', minWidth:120, title:'名称'},
                    {field:'logo', minWidth:100, title:'Logo', search:false, templet: ea.table.image},
                    {field:'url', minWidth:180, title:'链接'},
                    {field:'status', title:'状态', width:85, selectList:{0:'禁用',1:'启用'}, templet: ea.table.switch},
                    {width: 220, title: '操作', templet: ea.table.tool}
                ]],
            });
            ea.listen();
        },
        add: function () { ea.listen(); },
        edit: function () { ea.listen(); },
    };
});
