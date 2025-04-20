define(["jquery", "easy-admin"], function ($, ea) {

    var init = {
        table_elem: '#currentTable',
        table_render_id: 'currentTableRenderId',
        index_url: 'mall.goods/index',
        add_url: 'mall.goods/add',
        edit_url: 'mall.goods/edit',
        delete_url: 'mall.goods/delete',
        export_url: 'mall.goods/export',
        modify_url: 'mall.goods/modify',
        stock_url: 'mall.goods/stock',
    };

    return {

        index: function () {
            ea.table.render({
                init: init,
                toolbar: ['refresh',
                    [{
                        text: '添加',
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
                    {field: 'sort', width: 80, title: '排序', edit: 'text'},
                    {field: 'cate_id', minWidth: 80, title: '商品分类', selectList: cate,},
                    {field: 'title', minWidth: 80, title: '商品名称'},
                    {field: 'logo', minWidth: 80, title: '分类图片', search: false, templet: ea.table.image},
                    {field: 'market_price', width: 100, title: '市场价', templet: ea.table.price},
                    {field: 'discount_price', width: 100, title: '折扣价', templet: ea.table.price},
                    {field: 'total_stock', width: 100, title: '库存统计'},
                    {field: 'stock', width: 100, title: '剩余库存'},
                    {field: 'virtual_sales', width: 100, title: '虚拟销量'},
                    {field: 'sales', width: 80, title: '销量'},
                    {field: 'status', title: '状态', width: 85, selectList: {0: '禁用', 1: '启用'}, templet: ea.table.switch},
                    // 演示多选，实际数据库并无 status2 字段，搜索后会报错
                    {
                        field: 'status2', title: '演示多选', width: 105, search: 'xmSelect', selectList: {1: '模拟选项1', 2: '模拟选项2', 3: '模拟选项3', 4: '模拟选项4', 5: '模拟选项5'},
                        searchOp: 'in', templet: function (res) {
                            // 根据自己实际项目进行输出
                            return res?.status2 || '模拟数据'
                        }
                    },
                    {field: 'create_time', minWidth: 80, title: '创建时间', search: 'range'},
                    {
                        width: 250,
                        title: '操作',
                        templet: ea.table.tool,
                        operat: [
                            [{
                                templet: function (d) {
                                    return `<button type="button" class="layui-btn layui-btn-xs">自定义 ${d.id}</button>`
                                }
                            }, {
                                text: '编辑',
                                url: init.edit_url,
                                method: 'open',
                                auth: 'edit',
                                class: 'layui-btn layui-btn-xs layui-btn-success',
                            }, {
                                text: '入库',
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

            let colors = [
                '#f10f0f', //  红色
                '#ffaf00', //  橙色
                '#FF69B4', //  猛男粉
                '#0087ff', //  蓝色
                '#00ff00', //  青青草原
            ];
            var demo1 = xmSelect.render({
                el: '#demo1',
                name: 'xxx', // form表单提交的name
                theme: {color: colors[Math.floor(Math.random() * colors.length)]},
                data: [
                    {name: 'Make', value: 1},
                    {name: 'PHP', value: 2},
                    {name: 'Great Again', value: 3},
                ]
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

            let colors = [
                '#f10f0f', //  红色
                '#ffaf00', //  橙色
                '#FF69B4', //  猛男粉
                '#0087ff', //  蓝色
                '#00ff00', //  青青草原
            ];
            var demo1 = xmSelect.render({
                el: '#demo1',
                name: 'xxx', // form表单提交的name
                theme: {color: colors[Math.floor(Math.random() * colors.length)]},
                data: [
                    {name: 'Make', value: 1},
                    {name: 'PHP', value: 2, selected: true,},
                    {name: 'Great Again', value: 3, selected: true,},
                ]
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
        let url = ea.url('mall.goods/aiOptimization')
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
