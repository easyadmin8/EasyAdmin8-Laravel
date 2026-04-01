<?php

namespace App\Http\Controllers\admin\website;

use App\Http\Controllers\common\AdminController;
use App\Models\WebsiteBanner;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: 'Banner 管理')]
class BannerController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new WebsiteBanner();
        
    }
}
