<?php

namespace App\Http\Controllers\admin\website;

use App\Http\Controllers\common\AdminController;
use App\Models\WebsiteHotKeyword;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '热搜关键词管理')]
class HotKeywordController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new WebsiteHotKeyword();
        
    }
}
