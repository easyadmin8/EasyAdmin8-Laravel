define(["jquery", "easy-admin"], function ($, ea) {
    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'website/article_category/index',
        add_url: 'website/article_category/add',
        edit_url: 'website/article_category/edit',
        delete_url: 'website/article_category/delete',
        export_url: 'website/article_category/export',
        modify_url: 'website/article_category/modify',
    };
    return {
        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: "checkbox"},
                    {field:'id', width:80, title:'ID', searchOp:'='},
                    {field:'sort', width:80, title:'排序', edit:'text'},
                    {field:'title', minWidth:120, title:'分类名称'},
                    {field:'slug', minWidth:120, title:'别名'},
                    {field:'summary', minWidth:180, title:'简介'},
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
