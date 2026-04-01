define(["jquery", "easy-admin"], function ($, ea) {
    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'website/banner/index',
        add_url: 'website/banner/add',
        edit_url: 'website/banner/edit',
        delete_url: 'website/banner/delete',
        export_url: 'website/banner/export',
        modify_url: 'website/banner/modify',
    };
    return {
        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: "checkbox"},
                    {field:'id', width:80, title:'ID', searchOp:'='},
                    {field:'sort', width:80, title:'排序', edit:'text'},
                    {field:'title', minWidth:160, title:'标题'},
                    {field:'subtitle', minWidth:180, title:'副标题'},
                    {field:'image', minWidth:100, title:'图片', search:false, templet: ea.table.image},
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
