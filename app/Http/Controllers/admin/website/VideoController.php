<?php

namespace App\Http\Controllers\admin\website;

use App\Http\Controllers\common\AdminController;
use App\Models\WebsiteVideo;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '视频资料管理')]
class VideoController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new WebsiteVideo();
        
        $this->assign([]);

    }
}
