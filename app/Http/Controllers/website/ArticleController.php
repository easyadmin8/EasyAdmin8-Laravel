<?php

namespace App\Http\Controllers\website;

use Illuminate\View\View;

class ArticleController extends BaseController
{
    public function news(): View
    {
        $listing = $this->websiteData->articleList('news');
        return $this->render('website.article.index', [
            'listing' => $listing,
            'pageTitle' => $listing['category']['title'] ?? '新闻资讯',
            'metaDescription' => $listing['banner']['summary'] ?? '新闻资讯列表页',
        ]);
    }

    public function support(): View
    {
        $listing = $this->websiteData->articleList('support');
        return $this->render('website.article.index', [
            'listing' => $listing,
            'pageTitle' => $listing['category']['title'] ?? '技术支持',
            'metaDescription' => $listing['banner']['summary'] ?? '技术支持列表页',
        ]);
    }

    public function literature(): View
    {
        $listing = $this->websiteData->articleList('literature');
        return $this->render('website.article.index', [
            'listing' => $listing,
            'pageTitle' => $listing['category']['title'] ?? '文献引用',
            'metaDescription' => $listing['banner']['summary'] ?? '文献引用列表页',
        ]);
    }

    public function show(string $slug): View
    {
        $detail = $this->websiteData->articleDetail($slug);
        abort_if(empty($detail), 404);
        return $this->render('website.article.show', [
            'detail' => $detail,
            'pageTitle' => $detail['seo_title'] ?: $detail['title'],
            'metaDescription' => $detail['seo_description'] ?: ($detail['summary'] ?: ''),
            'metaKeywords' => $detail['seo_keywords'] ?: '',
        ]);
    }
}
