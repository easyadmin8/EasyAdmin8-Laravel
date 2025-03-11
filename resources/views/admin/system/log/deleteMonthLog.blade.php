@include('admin.layout.head')
<div class="layuimini-container">

    <form id="app-form" class="layui-form layuimini-form">

        <div class="layui-form-item">

            <div class="layui-input-group">
                <div class="layui-input-prefix layui-input-split">{{ea_trans('delete',false)}}</div>
                <label>
                    <input type="number" name="month" lay-affix="number" placeholder="" min="1" class="layui-input" value="3">
                </label>
                <div class="layui-input-suffix layui-input-split">{{ea_trans('Months ago Logs',true,'common')}}</div>
            </div>

        </div>

        <div class="hr-line"></div>
        <div class="layui-form-item text-center">
            <button type="button" class="layui-btn" lay-submit lay-filter="submit">{{ea_trans('confirm',false)}}</button>
        </div>
    </form>

</div>
@include('admin.layout.foot')
