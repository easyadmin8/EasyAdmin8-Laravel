define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'mall/goods/index',
        add_url: 'mall/goods/add',
        edit_url: 'mall/goods/edit',
        delete_url: 'mall/goods/delete',
        export_url: 'mall/goods/export',
        modify_url: 'mall/goods/modify',
        stock_url: 'mall/goods/stock',
    };

    return {

        index: function () {
            ea.table.render({
                init: init,
                css: [
                    '.layui-table-cell{height: 50px; line-height: 40px;}',
                ].join(''),
                toolbar: ['refresh',
                    [{
                        text: __('add'),
                        url: init.add_url,
                        method: 'open',
                        auth: 'add',
                        class: 'layui-btn layui-btn-normal layui-btn-sm',
                        icon: 'fa fa-plus ',
                    }],
                    'delete', 'export'],
                cols: [[
                    {type: "checkbox"},
                    {field: 'id', width: 80, title: 'ID', searchOp: '='},
                    {field: 'sort', width: 100, title: __('sort'), edit: 'text'},
                    {field: 'cate_id', minWidth: 80, title: '商品分类', selectList: cate,},
                    {field: 'title', minWidth: 120, title: __('goods name')},
                    {field: 'logo', minWidth: 120, title: __('goods image'), search: false, templet: ea.table.image},
                    {field: 'market_price', width: 100, title: __('goods price'), templet: ea.table.price},
                    {field: 'discount_price', width: 100, title: __('goods discounted price'), templet: ea.table.price},
                    {field: 'total_stock', width: 100, title: __('inventory statistics')},
                    {field: 'stock', width: 100, title: __('remaining inventory')},
                    {field: 'virtual_sales', width: 100, title: __('Virtual sales')},
                    {field: 'sales', width: 100, title: __('sales volume')},
                    {field: 'status', title: __('status'), width: 120, selectList: {0: __('disable'), 1: __('enable')}, templet: ea.table.switch},
                    // 演示多选，实际数据库并无 status2 字段，搜索后会报错
                    {
                        field: 'status2', title: __('multiple choice'), width: 105, search: 'xmSelect', selectList: {1: 'demo1', 2: 'demo2', 3: 'demo3', 4: 'demo4', 5: 'demo5'},
                        searchOp: 'in', templet: function (res) {
                            // 根据自己实际项目进行输出
                            return res?.status2 || 'demo'
                        }
                    },
                    {field: 'create_time', minWidth: 100, title: __('create time'), search: 'range'},
                    {
                        width: 250,
                        fixed: 'right',
                        title: __('operate'),
                        templet: ea.table.tool,
                        operat: [
                            [{
                                templet: function (d) {
                                    return `<button type="button" class="layui-btn layui-btn-xs">templet ${d.id}</button>`
                                }
                            }, {
                                text: __('edit'),
                                url: init.edit_url,
                                method: 'open',
                                auth: 'edit',
                                class: 'layui-btn layui-btn-xs layui-btn-success',
                            }, {
                                text: __('stock'),
                                url: init.stock_url,
                                method: 'open',
                                auth: 'stock',
                                class: 'layui-btn layui-btn-xs layui-btn-normal',
                                visible: function (row) {
                                    return row.status === 1;
                                }
                            }],
                            'delete']
                    }
                ]],
            });

            ea.listen();
        },
        add: function () {
            layui.util.on({
                AiOptimization: function (data) {
                    let layOn = $(data).attr('lay-on')
                    $(data).attr('lay-on', layOn + 'Loading')
                    aiOptimization(data)
                },
            })
            ea.listen();
        },
        edit: function () {
            layui.util.on({
                AiOptimization: function (data) {
                    let layOn = $(data).attr('lay-on')
                    $(data).attr('lay-on', layOn + 'Loading')
                    aiOptimization(data)
                },
            })
            ea.listen();
        },
        stock: function () {
            ea.listen();
        },
    };

    function aiOptimization(data) {
        let layOn = $(data).attr('lay-on')
        let title = $('input[name="title"]').val()

        // 告诉AI 你需要做什么
        let message = `优化这个标题 ${title}`

        if ($.trim(title) === '') {
            ea.msg.error('标题不能为空', function () {
                $(data).attr('lay-on', layOn.split('Loading')[0])
            })
            return false
        }
        let url = ea.url('mall/goods/aiOptimization')
        ea.request.post({url: url, data: {message: message}}, function (res) {
            let content = res.data?.choices[0]?.message?.content
            // stream 为true 时，AI 内容会逐字输出
            let stream = true
            ea.ai.chat(content, {stream: stream}, function () {
                $(data).attr('lay-on', layOn.split('Loading')[0])
            })
        }, function (error) {
            ea.msg.error(error.msg, function () {
                $(data).attr('lay-on', layOn.split('Loading')[0])
            })
        })
    }
});
