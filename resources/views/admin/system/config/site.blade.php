<form id="app-form" class="layui-form layuimini-form">

    <div class="layui-form-item">
        <label class="layui-form-label">{{ea_trans('site name',true,'common')}}</label>
        <div class="layui-input-block">
            <input type="text" name="site_name" class="layui-input" lay-verify="required" placeholder="{{ea_trans('Please Enter',false)}}" value="{{sysconfig('site','site_name')}}">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">{{ea_trans('ICON icon',true,'common')}}</label>
        <div class="layui-input-block layuimini-upload">
            <input name="site_ico" class="layui-input layui-col-xs6" lay-verify="required" placeholder="{{ea_trans('Please Enter',false)}}" value="{{sysconfig('site','site_ico')}}">
            <div class="layuimini-upload-btn">
                <span><a class="layui-btn" data-upload="site_ico" data-upload-number="one" data-upload-exts="ico"><i class="fa fa-upload"></i> {{ea_trans('upload',false)}}</a></span>
                <span><a class="layui-btn layui-btn-normal" id="select_site_ico" data-upload-select="site_ico" data-upload-number="one"><i class="fa fa-list"></i> {{ea_trans('select',false)}}</a></span>
            </div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">{{ea_trans('Background image',true,'common')}}</label>
        <div class="layui-input-block layuimini-upload">
            <input name="admin_background" class="layui-input layui-col-xs6" placeholder="#333333" value="{{sysconfig('site','admin_background')}}">
            <div class="layuimini-upload-btn">
                <span><a class="layui-btn" data-upload="admin_background" data-upload-number="one" data-upload-exts="png|jpg|jpeg"><i class="fa fa-upload"></i> {{ea_trans('upload',false)}}</a></span>
                <span><a class="layui-btn layui-btn-normal" id="select_admin_background" data-upload-select="admin_background" data-upload-number="one"><i class="fa fa-list"></i> {{ea_trans('select',false)}}</a></span>
            </div>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">{{ea_trans('Version',true,'common')}}</label>
        <div class="layui-input-block">
            <input type="text" name="site_version" class="layui-input" lay-verify="required" placeholder="{{ea_trans('Please Enter',false)}}" value="{{sysconfig('site','site_version')}}">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">{{ea_trans('ICP',true,'common')}}</label>
        <div class="layui-input-block">
            <input type="text" name="site_beian" class="layui-input" lay-verify="required" placeholder="{{ea_trans('Please Enter',false)}}" value="{{sysconfig('site','site_beian')}}">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">{{ea_trans('Copyright',true,'common')}}</label>
        <div class="layui-input-block">
            <input type="text" name="site_copyright" class="layui-input" lay-verify="required" placeholder="{{ea_trans('Please Enter',false)}}" value="{{sysconfig('site','site_copyright')}}">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">{{ea_trans('Editor',true,'common')}}</label>
        <div class="layui-input-block">
            @foreach($editor_types as $key=>$val)
                <input type="radio" name="editor_type" lay-filter="editor_type" value="{{$key}}" title="{{$val}}" @if($key==sysconfig('site','editor_type')) checked="" @endif>
            @endforeach
            <br>
        </div>
    </div>

    <div class="hr-line"></div>
    <div class="layui-form-item text-center">
        <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm" lay-submit="system/config/save" data-refresh="false">{{ea_trans('confirm',false)}}</button>
        <button type="reset" class="layui-btn layui-btn-primary layui-btn-sm">{{ea_trans('reset',false)}}</button>
    </div>

</form>
