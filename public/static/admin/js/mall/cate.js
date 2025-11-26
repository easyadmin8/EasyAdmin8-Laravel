define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'mall/cate/index',
        add_url: 'mall/cate/add',
        edit_url: 'mall/cate/edit',
        delete_url: 'mall/cate/delete',
        export_url: 'mall/cate/export',
        modify_url: 'mall/cate/modify',
    };

    return {

        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: "checkbox"},
                    {field: 'id', width: 100, title: 'ID', searchOp: '='},
                    {field: 'sort', width: 100, title: __('sort'), edit: 'text'},
                    {field: 'title', minWidth: 100, title: __('cate name')},
                    {field: 'image', minWidth: 100, title: __('cate name'), search: false, templet: ea.table.image},
                    {field: 'remark', minWidth: 100, title: __('remark')},
                    {field: 'status', title: __('status'), width: 120, search: 'select', selectList: {0: __('disable'), 1: __('enable')}, templet: ea.table.switch},
                    {field: 'create_time', minWidth: 100, title: __('create time'), search: 'range'},
                    {width: 250, title: __('operate'), templet: ea.table.tool}
                ]],
            });

            ea.listen();
        },
        add: function () {
            ea.listen();
        },
        edit: function () {
            ea.listen();
        },
    };
});
