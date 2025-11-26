@include('admin.layout.head')
<div class="layuimini-container">
    <form id="app-form" class="layui-form layuimini-form layui-form-pane">

        <div class="layui-form-item">
            <label class="layui-form-label">{{ea_trans('goods cate',true,'common')}}</label>
            <div class="layui-input-block">
                <select name="cate_id" lay-verify="required" data-select="{{__url('mall/cate/index')}}" data-fields="id,title" data-value="{{$row['cate_id']}}">
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">{{ea_trans('goods cate',true,'common')}}2</label>
            <div class="layui-input-block">
                <select name="cate_id" lay-verify="required">
                    @foreach($cate as $key=>$vo)
                        <option value="{{$key}}" @if($key == $row['cate_id']) selected @endif>{{$vo}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">{{ea_trans('goods cate',true,'common')}}3</label>
            <div data-show="switchSelect" data-list='{{json_encode($cate)}}' data-name="cate_id" data-value="{{$row['cate_id']}}" data-target="radio"></div>
        </div>

        <div class="layui-form-item">
            <div class="layui-row">
                <label class="layui-form-label required">{{ea_trans('goods name',true,'common')}}</label>
                <div class="layui-input-block layui-col-space5">
                    <div class="layui-col-xs10">
                        <div class="layui-input-wrap">
                            <input type="text" name="title" class="layui-input" lay-verify="required" placeholder="{{ea_trans('please enter',false)}}" value="{{$row['title']}}">
                        </div>
                    </div>
                    <div class="layui-col-xs2">
                        <button class="layui-btn layui-bg-purple layui-btn-fluid" type="button" lay-on="AiOptimization">AI优化</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">{{ea_trans('goods logo',true,'common')}}</label>
            <div class="layui-input-block layuimini-upload">
                <input name="logo" class="layui-input layui-col-xs6" lay-verify="required" placeholder="{{ea_trans('please enter',false)}}" value="{{$row['logo']}}">
                <div class="layuimini-upload-btn">
                    <span><a class="layui-btn" data-upload="logo" data-upload-number="one" data-upload-exts="png|jpg|ico|jpeg" data-upload-icon="image"><i class="fa fa-upload"></i> {{ea_trans('upload',false)}}</a></span>
                    <span><a class="layui-btn layui-btn-normal" id="select_logo" data-upload-select="logo" data-upload-number="one" data-upload-mimetype="image/*"><i class="fa fa-list"></i> {{ea_trans('select',false)}}</a></span>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">{{ea_trans('goods image',true,'common')}}</label>
            <div class="layui-input-block layuimini-upload">
                <input name="images" class="layui-input layui-col-xs6" lay-verify="required" placeholder="{{ea_trans('please enter',false)}}" value="{{$row['images']}}">
                <div class="layuimini-upload-btn">
                    <span><a class="layui-btn" data-upload="images" data-upload-number="more" data-upload-exts="png|jpg|ico|jpeg" data-upload-icon="image"><i class="fa fa-upload"></i> {{ea_trans('upload',false)}}</a></span>
                    <span><a class="layui-btn layui-btn-normal" id="select_images" data-upload-select="images" data-upload-number="more" data-upload-mimetype="image/*"><i class="fa fa-list"></i> {{ea_trans('select',false)}}</a></span>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">{{ea_trans('goods price',true,'common')}}</label>
            <div class="layui-input-block">
                <input type="text" name="market_price" class="layui-input" lay-verify="required" placeholder="{{ea_trans('please enter',false)}}" value="{{$row['market_price']}}">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">{{ea_trans('goods discounted price',true,'common')}}</label>
            <div class="layui-input-block">
                <input type="text" name="discount_price" class="layui-input" lay-verify="required" placeholder="{{ea_trans('please enter',false)}}" value="{{$row['discount_price']}}">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">{{ea_trans('virtual sales',true,'common')}}</label>
            <div class="layui-input-block">
                <input type="text" name="virtual_sales" class="layui-input" lay-verify="required" placeholder="{{ea_trans('please enter',false)}}" value="{{$row['virtual_sales']}}">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">{{ea_trans('goods desc',true,'common')}}</label>
            <div class="layui-input-block">
                {!!editor_textarea($row["describe"],'describe') !!}
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">{{ea_trans('goods sort',true,'common')}}</label>
            <div class="layui-input-block">
                <input type="number" name="sort" class="layui-input" placeholder="{{ea_trans('please enter',false)}}" value="{{$row['sort']}}">
            </div>
        </div>

        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">{{ea_trans('goods remark',true,'common')}}</label>
            <div class="layui-input-block">
                <textarea name="remark" class="layui-textarea" placeholder="{{ea_trans('please enter',false)}}">{{$row['remark']}}</textarea>
            </div>
        </div>

        <div class="hr-line"></div>
        <div class="layui-form-item text-center">
            <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm" lay-submit>{{ea_trans('confirm',false)}}</button>
            <button type="reset" class="layui-btn layui-btn-primary layui-btn-sm">{{ea_trans('reset',false)}}</button>
        </div>

    </form>
</div>
@include('admin.layout.foot')
