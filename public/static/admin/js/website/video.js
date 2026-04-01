define(["jquery", "easy-admin"], function ($, ea) {
    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'website/video/index',
        add_url: 'website/video/add',
        edit_url: 'website/video/edit',
        delete_url: 'website/video/delete',
        export_url: 'website/video/export',
        modify_url: 'website/video/modify',
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
                    {field:'cover', minWidth:100, title:'封面', search:false, templet: ea.table.image},
                    {field:'video_url', minWidth:180, title:'视频地址'},
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
