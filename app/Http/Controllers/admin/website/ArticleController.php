<?php

namespace App\Http\Controllers\admin\website;

use App\Http\Controllers\common\AdminController;
use App\Models\WebsiteArticle;
use App\Models\WebsiteArticleCategory;
use App\Http\Services\annotation\ControllerAnnotation;

#[ControllerAnnotation(title: '文章管理')]
class ArticleController extends AdminController
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new WebsiteArticle();
        $categories = (new WebsiteArticleCategory())->where('status', 1)->pluck('title', 'id')->toArray();
        $this->assign(compact('categories'));
    }
}
