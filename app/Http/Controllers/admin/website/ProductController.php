<?php

namespace App\Http\Controllers\admin\website;

use App\Http\Controllers\common\AdminController;
use App\Models\WebsiteProduct;
use App\Models\WebsiteProductCategory;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '产品管理')]
class ProductController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new WebsiteProduct();
        $categories = (new WebsiteProductCategory())->where('status', 1)->pluck('title', 'id')->toArray();
        $this->assign(compact('categories'));
    }
}
