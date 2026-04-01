define(["jquery", "easy-admin"], function ($, ea) {
    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'website/channel/index',
        add_url: 'website/channel/add',
        edit_url: 'website/channel/edit',
        delete_url: 'website/channel/delete',
        export_url: 'website/channel/export',
        modify_url: 'website/channel/modify',
    };
    return {
        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: "checkbox"},
                    {field:'id', width:80, title:'ID', searchOp:'='},
                    {field:'sort', width:80, title:'排序', edit:'text'},
                    {field:'title', minWidth:120, title:'栏目名称'},
                    {field:'slug', minWidth:120, title:'别名'},
                    {field:'type', minWidth:100, title:'类型', selectList: channelTypes},
                    {field:'is_nav', title:'导航', width:80, selectList:{0:'否',1:'是'}, templet: ea.table.switch},
                    {field:'status', title:'状态', width:85, selectList:{0:'禁用',1:'启用'}, templet: ea.table.switch},
                    {field:'create_time', minWidth:160, title:'创建时间', search:'range'},
                    {width: 220, title: '操作', templet: ea.table.tool}
                ]],
            });
            ea.listen();
        },
        add: function () { ea.listen(); },
        edit: function () { ea.listen(); },
    };
});
