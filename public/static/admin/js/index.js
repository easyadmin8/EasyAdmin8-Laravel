define(["jquery", "easy-admin", "echarts", "echarts-theme", "miniAdmin", "miniTab", "swiper"], function ($, ea, echarts, undefined, miniAdmin, miniTab) {

    return {
        index: function () {
            var options = {
                iniUrl: ea.url('ajax/initAdmin'),    // 初始化接口
                clearUrl: ea.url("ajax/clearCache"), // 缓存清理接口
                urlHashLocation: true,      // 是否打开hash定位
                bgColorDefault: false,      // 主题默认配置
                multiModule: true,          // 是否开启多模块
                menuChildOpen: false,       // 是否默认展开菜单
                loadingTime: 0,             // 初始化加载时间
                pageAnim: true,             // iframe窗口动画
                maxTabNum: 20,              // 最大的tab打开数量
            };
            miniAdmin.render(options);

            $('.login-out').on("click", function () {
                ea.request.get({
                    url: 'login/out',
                    prefix: true,
                }, function (res) {
                    ea.msg.success(res.msg, function () {
                        window.location = ea.url('login/index');
                    })
                });
            });
            layui.form.on('switch(header-theme-mode)', function (data) {
                let dark_mode = this.checked
                let that = $('iframe').contents()
                if (dark_mode) {
                    $('#layuicss-theme-dark').attr({
                        rel: "stylesheet",
                        type: "text/css",
                        href: "/static/admin/css/layui-theme-dark.css"
                    })
                        .appendTo("head");
                    that.find("html").addClass('dark')
                    $('html').addClass('dark')
                } else {
                    $('#layuicss-theme-dark').attr({
                        rel: "stylesheet",
                        type: "text/css",
                        href: ""
                    })
                    that.find("html").removeClass('dark')
                    $('html').removeClass('dark')
                }
            });
        },
        welcome: function () {
            miniTab.listen();

            new Swiper('.mySwiper', {
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
            })

            /**
             * 查看公告信息
             **/
            $('body').on('click', '.layuimini-notice', function () {
                var title = $(this).children('.layuimini-notice-title').text(),
                    noticeTime = $(this).children('.layuimini-notice-extra').text(),
                    content = $(this).children('.layuimini-notice-content').html();
                var html = '<div style="padding:15px 20px; text-align:justify; line-height: 22px;border-bottom:1px solid #e2e2e2;background-color: #2f4056;color: #ffffff">\n' +
                    '<div style="text-align: center;margin-bottom: 20px;font-weight: bold;border-bottom:1px solid #718fb5;padding-bottom: 5px"><h4 class="text-danger">' + title + '</h4></div>\n' +
                    '<div style="font-size: 12px">' + content + '</div>\n' +
                    '</div>\n';
                layer.open({
                    type: 1,
                    title: '系统公告' + '<span style="float: right;right: 1px;font-size: 12px;color: #b1b3b9;margin-top: 1px">' + noticeTime + '</span>',
                    area: '300px;',
                    shade: 0.8,
                    id: 'layuimini-notice',
                    btn: ['查看', '取消'],
                    btnAlign: 'c',
                    moveType: 1,
                    content: html,
                    success: function (layero) {
                        var btn = layero.find('.layui-layer-btn');
                        btn.find('.layui-layer-btn0').attr({
                            href: 'https://gitee.com/zhongshaofa/layuimini',
                            target: '_blank'
                        });
                    }
                });
            });

            /**
             * 报表功能
             */
            $(function () {
                $('#layui-version').text('v' + layui.v);
                let echartsRecords = echarts.init(document.getElementById('echarts-records'), 'walden');
                let optionRecords = {
                    title: {
                        text: '访问统计'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['邮件营销', '联盟广告', '视频广告', '直接访问', '搜索引擎']
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    toolbox: {
                        feature: {
                            saveAsImage: {}
                        }
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            name: '邮件营销',
                            type: 'line',
                            stack: '总量',
                            data: [120, 132, 101, 134, 90, 230, 210]
                        },
                        {
                            name: '联盟广告',
                            type: 'line',
                            stack: '总量',
                            data: [220, 182, 191, 234, 290, 330, 310]
                        },
                        {
                            name: '视频广告',
                            type: 'line',
                            stack: '总量',
                            data: [150, 232, 201, 154, 190, 330, 410]
                        },
                        {
                            name: '直接访问',
                            type: 'line',
                            stack: '总量',
                            data: [320, 332, 301, 334, 390, 330, 320]
                        },
                        {
                            name: '搜索引擎',
                            type: 'line',
                            stack: '总量',
                            data: [820, 932, 901, 934, 1290, 1330, 1320]
                        }
                    ]
                };
                echartsRecords.setOption(optionRecords);
                window.addEventListener("resize", function () {
                    echartsRecords.resize();
                });
            })

            let util = layui.util;
            util.on({
                showComposerInfo: function () {
                    // <div style="padding: 25px;">12313</div>
                    let html = ``
                    ea.request.get({
                        url: ea.url('ajax/composerInfo'),
                    }, function (success) {
                        let data = success.data
                        data.forEach(function (item) {
                            html += `${item.name}  ${item.version}\r\n`
                        })
                        html = `<pre class="layui-code code-demo">${html}</pre>`
                        layer.open({
                            type: 1,
                            title: 'composer 信息',
                            area: ['50%', '90%'],
                            shade: 0.8,
                            shadeClose: true,
                            scrollbar: false,
                            content: html,
                            success: function () {
                                layui.code({elem: '.code-demo', theme: 'dark', lang: 'php'});
                            }
                        })
                    }, function (error) {
                        console.error(error)
                        return false;
                    })

                }
            })
        },
        editAdmin: function () {
            let form = layui.form
            form.on('radio(loginType-filter)', function (data) {
                let elem = data.elem
                let value = elem.value
                if (value === '2') {
                    let width = screen.width < 768 ? '85%' : '60%'
                    ea.open('绑定谷歌验证码', ea.url('index/set2fa'), width, '75%')
                }
            });
            ea.listen();
        },
        editPassword: function () {
            ea.listen();
        },
        set2fa: function () {
            ea.listen();
        },
    };
});
