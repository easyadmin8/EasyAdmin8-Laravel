define(["jquery", "easy-admin", "treetable", "iconPickerFa", "autocomplete"], function ($, ea) {

    var table = layui.table,
        treetable = layui.treetable,
        iconPickerFa = layui.iconPickerFa,
        autocomplete = layui.autocomplete;

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'system/menu/index',
        add_url: 'system/menu/add',
        delete_url: 'system/menu/delete',
        edit_url: 'system/menu/edit',
        modify_url: 'system/menu/modify',
    };

    return {
        index: function () {

            var renderTable = function () {
                layer.load(2);
                treetable.render({
                    where: {limit: 9999},
                    treeColIndex: 1,
                    treeSpid: 0,
                    homdPid: 99999999,
                    treeIdName: 'id',
                    treePidName: 'pid',
                    url: ea.url(init.index_url),
                    elem: init.table_elem,
                    id: init.table_render_id,
                    toolbar: '#toolbar',
                    page: false,
                    skin: 'line',

                    // @todo 不直接使用ea.table.render(); 进行表格初始化, 需要使用 ea.table.formatCols(); 方法格式化`cols`列数据
                    cols: ea.table.formatCols([[
                        {type: 'checkbox'},
                        {
                            field: 'title', width: 250, title: __('menu name'), align: 'left', templet: function (d) {
                                return __(d.title)
                            }
                        },
                        {field: 'icon', width: 100, title: __('menu icon'), templet: ea.table.icon},
                        {field: 'href', minWidth: 120, title: __('menu url')},
                        {
                            field: 'is_home',
                            width: 100,
                            title: __('menu type'),
                            templet: function (d) {
                                if (d.pid === 99999999) {
                                    return `<span class="layui-badge layui-bg-blue">${__('home')}</span>`;
                                }
                                if (d.pid === 0) {
                                    return `<span class="layui-badge layui-bg-gray">${__('module')}</span>`;
                                } else {
                                    return `<span class="layui-badge-rim">${__('menu')}</span>`;
                                }
                            }
                        },
                        {field: 'status', title: __('menu status'), width: 120, templet: ea.table.switch},
                        {field: 'sort', width: 120, title: __('menu sort'), edit: 'text'},
                        {
                            minWidth: 230,
                            title: __('operate'),
                            templet: ea.table.tool,
                            operat: [
                                [{
                                    text: __('add subordinate'),
                                    url: init.add_url,
                                    method: 'open',
                                    auth: 'add',
                                    class: 'layui-btn layui-btn-xs layui-btn-normal',
                                }, {
                                    text: __('edit'),
                                    url: init.edit_url,
                                    method: 'open',
                                    auth: 'edit',
                                    class: 'layui-btn layui-btn-xs layui-btn-success',
                                }],
                                'delete'
                            ]
                        }
                    ]], init),
                    done: function () {
                        layer.closeAll('loading');
                    }
                });
            };

            renderTable();

            $('body').on('click', '[data-treetable-refresh]', function () {
                renderTable();
            });

            $('body').on('click', '[data-treetable-delete]', function () {
                var tableId = $(this).attr('data-treetable-delete'),
                    url = $(this).attr('data-url');
                tableId = tableId || init.table_render_id;
                url = url != undefined ? ea.url(url) : window.location.href;
                var checkStatus = table.checkStatus(tableId),
                    data = checkStatus.data;
                if (data.length <= 0) {
                    ea.msg.error(__('please check the data to be deleted'));
                    return false;
                }
                var ids = [];
                $.each(data, function (i, v) {
                    ids.push(v.id);
                });
                ea.msg.confirm(__('confirm to delete'), function () {
                    ea.request.post({
                        url: url,
                        data: {
                            id: ids
                        },
                    }, function (res) {
                        ea.msg.success(res.msg, function () {
                            renderTable();
                        });
                    });
                });
                return false;
            });

            ea.table.listenSwitch({filter: 'status', url: init.modify_url});

            ea.table.listenEdit(init, 'currentTable', init.table_render_id, true);

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
                    }
                });
            })
            autocomplete.render({
                elem: $('#href')[0],
                url: ea.url('system/menu/getMenuTips'),
                template_val: '{{d.node}}',
                template_txt: '【{{d.title}}】 - {{d.node}}',
                onselect: function (resp) {
                }
            });

            ea.listen(function (data) {
                return data;
            }, function (res) {
                ea.msg.success(res.msg, function () {
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                    parent.$('[data-treetable-refresh]').trigger("click");
                });
            });
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
                    }
                });
            })
            autocomplete.render({
                elem: $('#href')[0],
                url: ea.url('system/menu/getMenuTips'),
                template_val: '{{d.node}}',
                template_txt: '【{{d.title}}】 - {{d.node}}',
                onselect: function (resp) {
                }
            });

            ea.listen(function (data) {
                return data;
            }, function (res) {
                ea.msg.success(res.msg, function () {
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                    parent.$('[data-treetable-refresh]').trigger("click");
                });
            });
        }
    };
});
