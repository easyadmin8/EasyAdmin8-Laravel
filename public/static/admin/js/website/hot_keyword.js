define(["jquery", "easy-admin"], function ($, ea) {
    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'website/hot_keyword/index',
        add_url: 'website/hot_keyword/add',
        edit_url: 'website/hot_keyword/edit',
        delete_url: 'website/hot_keyword/delete',
        export_url: 'website/hot_keyword/export',
        modify_url: 'website/hot_keyword/modify',
    };
    return {
        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: "checkbox"},
                    {field:'id', width:80, title:'ID', searchOp:'='},
                    {field:'sort', width:80, title:'排序', edit:'text'},
                    {field:'keyword', minWidth:160, title:'关键词'},
                    {field:'link', minWidth:200, title:'链接'},
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
