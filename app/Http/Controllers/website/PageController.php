<?php

namespace App\Http\Controllers\website;

use Illuminate\View\View;

class PageController extends BaseController
{
    public function about(): View
    {
        return $this->show('about-us');
    }

    public function agents(): View
    {
        return $this->show('agents');
    }

    public function show(string $slug): View
    {
        $page = $this->websiteData->pageBySlug($slug);
        abort_if(empty($page), 404);
        return $this->render('website.page.show', [
            'page' => $page,
            'pageTitle' => $page['seo_title'] ?: $page['title'],
            'metaDescription' => $page['seo_description'] ?: ($page['summary'] ?: ''),
            'metaKeywords' => $page['seo_keywords'] ?: '',
        ]);
    }
}
