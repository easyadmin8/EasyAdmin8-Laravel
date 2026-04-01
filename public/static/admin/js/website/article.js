define(["jquery", "easy-admin"], function ($, ea) {
    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'website/article/index',
        add_url: 'website/article/add',
        edit_url: 'website/article/edit',
        delete_url: 'website/article/delete',
        export_url: 'website/article/export',
        modify_url: 'website/article/modify',
    };
    return {
        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: "checkbox"},
                    {field:'id', width:80, title:'ID', searchOp:'='},
                    {field:'sort', width:80, title:'排序', edit:'text'},
                    {field:'category_id', minWidth:120, title:'分类', selectList: categories},
                    {field:'title', minWidth:200, title:'文章标题'},
                    {field:'cover', minWidth:100, title:'封面', search:false, templet: ea.table.image},
                    {field:'published_at', minWidth:120, title:'发布时间', search:'range'},
                    {field:'is_recommend', title:'推荐', width:80, selectList:{0:'否',1:'是'}, templet: ea.table.switch},
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
