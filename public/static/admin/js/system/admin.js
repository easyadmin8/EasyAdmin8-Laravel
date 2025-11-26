define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'system/admin/index',
        add_url: 'system/admin/add',
        edit_url: 'system/admin/edit',
        delete_url: 'system/admin/delete',
        modify_url: 'system/admin/modify',
        export_url: 'system/admin/export',
        password_url: 'system/admin/password',
    };

    return {

        index: function () {

            ea.table.render({
                init: init,
                cols: [[
                    {type: "checkbox"},
                    {field: 'id', width: 80, title: 'ID', searchOp: '='},
                    {field: 'sort', width: 80, title: __('sort'), edit: 'text'},
                    {field: 'username', minWidth: 80, title: __('account')},
                    {field: 'head_img', minWidth: 80, title: __('avatar'), search: false, templet: ea.table.image},
                    {field: 'phone', minWidth: 80, title: __('mobile')},
                    {field: 'login_num', minWidth: 80, title: __('times')},
                    {field: 'remark', minWidth: 80, title: __('remark')},
                    {field: 'status', title: __('status'), width: 85, search: 'select', selectList: {0: __('disable'), 1: __('enable')}, templet: ea.table.switch},
                    {field: 'create_time', minWidth: 80, title: __('create time'), search: 'range'},
                    {
                        width: 250,
                        title: __('operate'),
                        templet: ea.table.tool,
                        operat: [
                            'edit',
                            [{
                                text: __('Set password'),
                                url: init.password_url,
                                method: 'open',
                                auth: 'password',
                                class: 'layui-btn layui-btn-normal layui-btn-xs',
                            }],
                            'delete'
                        ]
                    }
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
        password: function () {
            ea.listen();
        }
    };
});
