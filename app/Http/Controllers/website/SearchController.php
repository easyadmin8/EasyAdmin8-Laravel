<?php

namespace App\Http\Controllers\website;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends BaseController
{
    public function index(Request $request): View
    {
        $result = $this->websiteData->search($request->get('keyword'));
        return $this->render('website.search.index', [
            'result' => $result,
            'pageTitle' => '搜索结果',
            'metaDescription' => '官网站内搜索结果页',
        ]);
    }
}
