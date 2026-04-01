define(["jquery", "easy-admin"], function ($, ea) {
    var element = layui.element;
    return {
        index: function () {
            var _group = 'website';
            element.on('tab(websiteConfigTab)', function () {
                _group = $(this).data('group');
            });
            layui.form.on('submit', function (data) {
                data.field['group'] = _group;
            });
            ea.listen();
        }
    };
});
