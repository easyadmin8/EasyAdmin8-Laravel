define(["jquery", "easy-admin"], function ($, ea) {
    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'website/product_category/index',
        add_url: 'website/product_category/add',
        edit_url: 'website/product_category/edit',
        delete_url: 'website/product_category/delete',
        export_url: 'website/product_category/export',
        modify_url: 'website/product_category/modify',
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
                    {field:'cover', minWidth:100, title:'封面', search:false, templet: ea.table.image},
                    {field:'is_featured', title:'首页推荐', width:100, selectList:{0:'否',1:'是'}, templet: ea.table.switch},
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
