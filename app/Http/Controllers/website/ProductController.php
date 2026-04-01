<?php

namespace App\Http\Controllers\website;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends BaseController
{
    public function index(Request $request): View
    {
        $listing = $this->websiteData->productList($request->get('category'));
        return $this->render('website.product.index', [
            'listing' => $listing,
            'pageTitle' => ($listing['currentCategory']['title'] ?? '产品中心'),
            'metaDescription' => $listing['banner']['summary'] ?? '产品中心列表页',
        ]);
    }

    public function show(string $slug): View
    {
        $detail = $this->websiteData->productDetail($slug);
        abort_if(empty($detail), 404);
        return $this->render('website.product.show', [
            'detail' => $detail,
            'pageTitle' => $detail['seo_title'] ?: $detail['title'],
            'metaDescription' => $detail['seo_description'] ?: ($detail['summary'] ?: ''),
            'metaKeywords' => $detail['seo_keywords'] ?: '',
        ]);
    }
}
