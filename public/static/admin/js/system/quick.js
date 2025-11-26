define(["jquery", "easy-admin", "iconPickerFa", "autocomplete"], function ($, ea) {

    var iconPickerFa = layui.iconPickerFa,
        autocomplete = layui.autocomplete;

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'system/quick/index',
        add_url: 'system/quick/add',
        edit_url: 'system/quick/edit',
        delete_url: 'system/quick/delete',
        export_url: 'system/quick/export',
        modify_url: 'system/quick/modify',
    };

    return {

        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: "checkbox"},
                    {field: 'id', width: 80, title: 'ID', searchOp: '='},
                    {field: 'sort', width: 80, title: __('sort'), edit: 'text'},
                    {field: 'title', minWidth: 80, title: __('title')},
                    {field: 'icon', width: 80, title: __('icon'), templet: ea.table.icon},
                    {field: 'href', minWidth: 120, title: 'href'},
                    {field: 'remark', minWidth: 80, title: __('remark')},
                    {field: 'status', title: __('status'), width: 120, search: 'select', selectList: {0: __('disable'), 1: __('enable')}, templet: ea.table.switch},
                    {field: 'create_time', minWidth: 80, title: __('create time'), search: 'range'},
                    {width: 250, title: __('operate'), templet: ea.table.tool, operat: ['edit', 'delete']}
                ]],
            });

            ea.listen();
        },
        add: function () {
            $(function () {
                iconPickerFa.render({
                    elem: '#icon',
                    url: PATH_CONFIG.iconLess,
                    limit: 12,
                    click: function (data) {
                        $('#icon').val('fa ' + data.icon);
                    },
                    success: function (d) {
                        console.log(d);
                    }
                });
            })
            autocomplete.render({
                elem: $('#href')[0],
                url: ea.url('system/menu/getMenuTips'),
                template_val: '{{d.node}}',
                template_txt: '{{d.node}} <span class="layui-badge">{{d.title}}</span>',
                onselect: function (resp) {
                }
            });

            ea.listen();
        },
        edit: function () {
            $(function () {
                iconPickerFa.render({
                    elem: '#icon',
                    url: PATH_CONFIG.iconLess,
                    limit: 12,
                    click: function (data) {
                        $('#icon').val('fa ' + data.icon);
                    },
                    success: function (d) {
                        console.log(d);
                    }
                });
            })
            autocomplete.render({
                elem: $('#href')[0],
                url: ea.url('system/menu/getMenuTips'),
                template_val: '{{d.node}}',
                template_txt: '{{d.node}} <span class="layui-badge">{{d.title}}</span>',
                onselect: function (resp) {
                }
            });

            ea.listen();
        },
    };
});
