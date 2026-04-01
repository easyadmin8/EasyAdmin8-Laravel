<?php

namespace App\Http\Controllers\admin\website;

use App\Http\Controllers\common\AdminController;
use App\Models\WebsiteChannel;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '导航管理')]
class ChannelController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new WebsiteChannel();
        $channelTypes = [
            'single' => '单页',
            'product' => '产品列表',
            'article' => '文章列表',
            'video' => '视频列表',
            'link' => '外链',
        ];
        $pidChannelList = $this->model->getPidChannelList();
        $this->assign(compact('channelTypes', 'pidChannelList'));
    }
}
