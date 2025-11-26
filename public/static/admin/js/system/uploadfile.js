define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'system/uploadfile/index',
        add_url: 'system/uploadfile/add',
        edit_url: 'system/uploadfile/edit',
        delete_url: 'system/uploadfile/delete',
        modify_url: 'system/uploadfile/modify',
        export_url: 'system/uploadfile/export',
    };

    return {

        index: function () {
            ea.table.render({
                init: init,
                cols: [[
                    {type: "checkbox"},
                    {field: 'id', width: 80, title: 'ID', searchOp: '='},
                    {field: 'upload_type', minWidth: 80, title: __('location'), search: 'select', selectList: upload_types},
                    {field: 'url', minWidth: 80, search: false, title: __('information'), templet: ea.table.image},
                    {field: 'url', minWidth: 120, title: __('address'), templet: ea.table.url},
                    {field: 'original_name', minWidth: 80, title: __('filename')},
                    {field: 'mime_type', minWidth: 80, title: __('mime type')},
                    {field: 'file_ext', minWidth: 80, title: __('extension')},
                    {field: 'create_time', minWidth: 80, title: __('create time'), search: 'range'},
                    {width: 250, title: __('operate'), templet: ea.table.tool, operat: ['delete']}
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
