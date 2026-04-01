<?php

namespace App\Http\Controllers\admin\website;

use App\Http\Controllers\common\AdminController;
use App\Models\WebsiteProductCategory;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '产品分类管理')]
class ProductCategoryController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new WebsiteProductCategory();
        
    }
}
