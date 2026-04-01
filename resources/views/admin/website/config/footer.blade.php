<form id="app-form-footer" class="layui-form layuimini-form">
    <div class="layui-form-item layui-form-text"><label class="layui-form-label">页脚简介</label><div class="layui-input-block"><textarea name="footer_intro" class="layui-textarea">{{ sysconfig('website_footer','footer_intro') }}</textarea></div></div>
    <div class="layui-form-item layui-form-text"><label class="layui-form-label">联系提示</label><div class="layui-input-block"><textarea name="contact_tip" class="layui-textarea">{{ sysconfig('website_footer','contact_tip') }}</textarea></div></div>
    <div class="layui-form-item"><label class="layui-form-label">代理电话</label><div class="layui-input-block"><input type="text" name="agent_phone" class="layui-input" value="{{ sysconfig('website_footer','agent_phone') }}"></div></div>
    <div class="layui-form-item"><label class="layui-form-label">代理邮箱</label><div class="layui-input-block"><input type="text" name="agent_email" class="layui-input" value="{{ sysconfig('website_footer','agent_email') }}"></div></div>
    <div class="hr-line"></div><div class="layui-form-item text-center"><button type="submit" class="layui-btn layui-btn-normal layui-btn-sm" lay-submit="website/config/save" data-refresh="false">确认</button><button type="reset" class="layui-btn layui-btn-primary layui-btn-sm">重置</button></div>
</form>
