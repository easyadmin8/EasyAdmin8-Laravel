<?php

namespace App\Http\Controllers\website;

use Illuminate\View\View;

class VideoController extends BaseController
{
    public function index(): View
    {
        $listing = $this->websiteData->videoList();
        return $this->render('website.video.index', [
            'listing' => $listing,
            'pageTitle' => '视频中心',
            'metaDescription' => $listing['banner']['summary'] ?? '视频列表页',
        ]);
    }

    public function show(string $slug): View
    {
        $detail = $this->websiteData->videoDetail($slug);
        abort_if(empty($detail), 404);
        return $this->render('website.video.show', [
            'detail' => $detail,
            'pageTitle' => $detail['seo_title'] ?: $detail['title'],
            'metaDescription' => $detail['seo_description'] ?: ($detail['summary'] ?: ''),
            'metaKeywords' => $detail['seo_keywords'] ?: '',
        ]);
    }
}
