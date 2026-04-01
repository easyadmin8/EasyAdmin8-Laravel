<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\common\Controller;
use App\Support\WebsiteData;
use Illuminate\View\View;

abstract class BaseController extends Controller
{
    protected WebsiteData $websiteData;
    protected array $sharedData = [];

    protected function initialize()
    {
        $this->websiteData = app(WebsiteData::class);
        $this->sharedData = $this->websiteData->shared();
        view()->share($this->sharedData);
    }

    protected function render(string $view, array $data = []): View
    {
        return view($view, array_merge($this->sharedData, $data));
    }
}
