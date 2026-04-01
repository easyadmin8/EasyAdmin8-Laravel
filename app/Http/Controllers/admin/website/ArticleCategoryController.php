<?php

namespace App\Http\Controllers\admin\website;

use App\Http\Controllers\common\AdminController;
use App\Models\WebsiteArticleCategory;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '文章分类管理')]
class ArticleCategoryController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new WebsiteArticleCategory();
        
    }
}
