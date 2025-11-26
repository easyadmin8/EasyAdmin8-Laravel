define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'system/node/index',
        add_url: 'system/node/add',
        edit_url: 'system/node/edit',
        delete_url: 'system/node/delete',
        modify_url: 'system/node/modify',
    };

    return {

        index: function () {
            ea.table.render({
                init: init,
                search: false,
                page: false,
                toolbar: ['refresh',
                    [{
                        text: __('Update nodes'),
                        title: __('Confirm to update the new node'),
                        url: 'system/node/refreshNode?force=0',
                        method: 'request',
                        auth: 'refresh',
                        class: 'layui-btn layui-btn-success layui-btn-sm',
                        icon: 'fa fa-hourglass',
                        extend: 'data-table="' + init.table_render_id + '"',
                    }, {
                        text: __('Force update of nodes'),
                        title: __('Force update prompt'),
                        url: 'system/node/refreshNode?force=1',
                        method: 'request',
                        auth: 'refresh',
                        class: 'layui-btn layui-btn-sm layui-btn-normal',
                        icon: 'fa fa-hourglass',
                        extend: 'data-table="' + init.table_render_id + '"',
                    }, {

                        text: __('Clear invalid nodes'),
                        title: __('Are you sure to clear the failed nodes'),
                        url: 'system/node/clearNode',
                        method: 'request',
                        auth: 'clear',
                        class: 'layui-btn layui-btn-sm layui-btn-danger',
                        icon: 'fa fa-trash-o',
                        extend: 'data-table="' + init.table_render_id + '"',
                    }
                    ]],
                cols: [[
                    {
                        field: 'node', minWidth: 200, align: 'left', title: __('node'), templet: function (d) {
                            return d.node;
                        }
                    },
                    {field: 'title', minWidth: 80, title: __('title') + ' <i class="table-edit-tips color-red">*</i>', edit: 'text'},
                    {field: 'update_time', minWidth: 80, title: __('update time'), search: 'range'},
                    {field: 'is_auth', title: __('auth'), width: 85, search: 'select', selectList: {0: __('disable'), 1: __('enable')}, templet: ea.table.switch},
                ]],
            });

            ea.listen();
        },
        add: function () {
            ea.listen();
        },
        edit: function () {
            ea.listen();
        }
    };
});
