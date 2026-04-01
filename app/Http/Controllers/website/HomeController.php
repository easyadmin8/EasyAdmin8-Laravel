<?php

namespace App\Http\Controllers\website;

use Illuminate\View\View;

class HomeController extends BaseController
{
    public function index(): View
    {
        $home = $this->websiteData->home();
        $site = $home['site'];
        return $this->render('website.home.index', compact('home', 'site') + [
            'pageTitle' => $site['seo_title'] ?? $site['site_name'],
            'metaDescription' => $site['seo_description'] ?? $site['hero_summary'],
            'metaKeywords' => $site['seo_keywords'] ?? '',
        ]);
    }
}
