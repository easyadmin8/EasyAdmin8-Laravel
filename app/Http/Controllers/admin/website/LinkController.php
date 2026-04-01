<?php

namespace App\Http\Controllers\admin\website;

use App\Http\Controllers\common\AdminController;
use App\Models\WebsiteLink;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '友情链接管理')]
class LinkController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new WebsiteLink();
        
    }
}
