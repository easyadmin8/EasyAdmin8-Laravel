<?php

namespace App\Support;

use App\Models\WebsiteArticle;
use App\Models\WebsiteArticleCategory;
use App\Models\WebsiteBanner;
use App\Models\WebsiteChannel;
use App\Models\WebsiteHotKeyword;
use App\Models\WebsiteLink;
use App\Models\WebsiteProduct;
use App\Models\WebsiteProductCategory;
use App\Models\WebsiteVideo;
use App\Models\SystemConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WebsiteData
{
    public function shared(): array
    {
        return [
            'site'       => $this->site(),
            'navigation' => $this->navigation(),
            'hotWords'   => $this->hotKeywords(),
            'links'      => $this->links()->take(12)->values()->all(),
        ];
    }

    public function home(): array
    {
        return [
            'site'               => $this->site(),
            'navigation'         => $this->navigation(),
            'hotWords'           => $this->hotKeywords(),
            'banners'            => $this->banners(),
            'featuredCategories' => $this->featuredCategories(),
            'featuredProducts'   => $this->featuredProducts(),
            'newProducts'        => $this->newProducts(),
            'videos'             => $this->featuredVideos(),
            'articles'           => $this->latestArticles('news'),
            'links'              => $this->links(),
            'contact'            => $this->contact(),
        ];
    }

    public function site(): array
    {
        $fallbackName = sysconfig('site', 'site_name') ?: 'FUDA CELL';

        return [
            'site_name'         => $fallbackName,
            'brand_name'        => $this->config('website', 'brand_name', 'FUDA CELL'),
            'logo'              => $this->config('website', 'logo', sysconfig('site', 'logo_image') ?: '/static/common/images/logo-1.png'),
            'tagline'           => $this->config('website', 'tagline', 'Professional battery and digital accessory manufacturer'),
            'phone'             => $this->config('website', 'service_phone', '+86 400-888-6868'),
            'email'             => $this->config('website', 'service_email', 'sales@fudacell.com'),
            'address'           => $this->config('website', 'company_address', 'Shenzhen, Guangdong, China'),
            'work_time'         => $this->config('website', 'work_time', 'Mon - Sat 09:00 - 18:00'),
            'beian'             => sysconfig('site', 'site_beian') ?: '粤ICP备XXXXXXXX号',
            'copyright'         => sysconfig('site', 'site_copyright') ?: ('© ' . date('Y') . ' ' . $fallbackName . ' All Rights Reserved.'),
            'search_tip'        => $this->config('website', 'search_tip', 'Search products, support, literature and news'),
            'hero_title'        => $this->config('website', 'hero_title', 'Professional battery solutions for mobile digital products'),
            'hero_summary'      => $this->config('website', 'hero_summary', 'Focus on consumer batteries, replacement power solutions and digital accessories. Frontend pages are now connected to backend-managed content tables.'),
            'seo_title'         => $this->config('website', 'seo_title', $fallbackName),
            'seo_keywords'      => $this->config('website', 'seo_keywords', 'battery, accessories, oem, odm'),
            'seo_description'   => $this->config('website', 'seo_description', 'FUDA CELL professional battery and digital accessories website.'),
            'footer_intro'      => $this->config('website_footer', 'footer_intro', 'A modern energy solution supplier focusing on stable quality, agile delivery and long-term cooperation.'),
            'footer_contact_tip'=> $this->config('website_footer', 'contact_tip', 'Please contact us for OEM, ODM and distributor cooperation.'),
        ];
    }

    public function contact(): array
    {
        return [
            'phone'      => $this->config('website', 'service_phone', '+86 400-888-6868'),
            'email'      => $this->config('website', 'service_email', 'sales@fudacell.com'),
            'address'    => $this->config('website', 'company_address', 'Shenzhen, Guangdong, China'),
            'work_time'  => $this->config('website', 'work_time', 'Mon - Sat 09:00 - 18:00'),
            'agent_phone'=> $this->config('website_footer', 'agent_phone', '+86 755-8888-0000'),
            'agent_email'=> $this->config('website_footer', 'agent_email', 'agent@fudacell.com'),
        ];
    }

    public function navigation(): array
    {
        if (Schema::hasTable('website_channel')) {
            $items = WebsiteChannel::query()
                ->where('status', 1)
                ->where('is_nav', 1)
                ->orderByDesc('sort')
                ->orderBy('id')
                ->get(['title', 'slug', 'type'])
                ->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'url'   => $this->channelUrl($item->type, $item->slug),
                    ];
                })
                ->filter(fn ($item) => !empty($item['url']))
                ->values()
                ->all();

            if (!empty($items)) {
                return $items;
            }
        }

        return [
            ['title' => '首页', 'url' => route('website.home')],
            ['title' => '关于我们', 'url' => route('website.about')],
            ['title' => '产品中心', 'url' => route('website.products.index')],
            ['title' => '技术支持', 'url' => route('website.support.index')],
            ['title' => '文献引用', 'url' => route('website.literature.index')],
            ['title' => '新闻资讯', 'url' => route('website.articles.index')],
            ['title' => '视频中心', 'url' => route('website.videos.index')],
            ['title' => '全国代理', 'url' => route('website.agents')],
        ];
    }

    public function hotKeywords(): array
    {
        if (Schema::hasTable('website_hot_keyword')) {
            $items = WebsiteHotKeyword::query()
                ->where('status', 1)
                ->orderByDesc('sort')
                ->orderBy('id')
                ->limit(8)
                ->get(['keyword', 'link'])
                ->map(fn ($item) => [
                    'title' => $item->keyword,
                    'url'   => $item->link ?: route('website.search', ['keyword' => $item->keyword]),
                ])
                ->all();

            if (!empty($items)) {
                return $items;
            }
        }

        return [
            ['title' => 'Li-ion battery', 'url' => route('website.search', ['keyword' => 'Li-ion battery'])],
            ['title' => 'Phone battery', 'url' => route('website.search', ['keyword' => 'Phone battery'])],
            ['title' => 'OEM', 'url' => route('website.search', ['keyword' => 'OEM'])],
            ['title' => 'ODM', 'url' => route('website.search', ['keyword' => 'ODM'])],
        ];
    }

    public function banners(): array
    {
        if (Schema::hasTable('website_banner')) {
            $items = WebsiteBanner::query()
                ->where('status', 1)
                ->orderByDesc('sort')
                ->orderBy('id')
                ->limit(3)
                ->get(['title', 'subtitle', 'image', 'link', 'button_text'])
                ->map(fn ($item) => [
                    'title'       => $item->title,
                    'subtitle'    => $item->subtitle,
                    'image'       => $item->image ?: '/static/common/images/logo-1.png',
                    'link'        => $item->link ?: route('website.products.index'),
                    'button_text' => $item->button_text ?: '查看详情',
                ])
                ->all();
            if (!empty($items)) {
                return $items;
            }
        }

        return [[
            'title' => 'Factory direct digital power solutions',
            'subtitle' => 'Fast sampling · Stable quality · Flexible customization',
            'image' => '/static/common/images/logo-1.png',
            'link' => route('website.products.index'),
            'button_text' => 'Browse products',
        ]];
    }

    public function featuredCategories(): array
    {
        if (Schema::hasTable('website_product_category')) {
            $items = WebsiteProductCategory::query()
                ->where('status', 1)
                ->where('is_featured', 1)
                ->orderByDesc('sort')
                ->orderBy('id')
                ->limit(6)
                ->get(['title', 'slug', 'summary', 'cover'])
                ->map(fn ($item) => [
                    'title'   => $item->title,
                    'summary' => $item->summary ?: 'Category summary',
                    'cover'   => $item->cover ?: '/static/common/images/logo-1.png',
                    'url'     => route('website.products.index', ['category' => $item->slug]),
                ])
                ->all();
            if (!empty($items)) {
                return $items;
            }
        }
        return [];
    }

    public function featuredProducts(): array
    {
        return $this->productItems('is_featured', 8);
    }

    public function newProducts(): array
    {
        return $this->productItems('is_new', 8);
    }

    public function productCategories(): Collection
    {
        if (!Schema::hasTable('website_product_category')) {
            return collect();
        }

        return WebsiteProductCategory::query()
            ->where('status', 1)
            ->orderByDesc('sort')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'summary', 'cover']);
    }

    public function productList(?string $categorySlug = null): array
    {
        $categories = $this->productCategories();
        $query = WebsiteProduct::query()->where('status', 1);
        $currentCategory = null;
        if ($categorySlug && $categories->isNotEmpty()) {
            $currentCategory = $categories->firstWhere('slug', $categorySlug);
            if ($currentCategory) {
                $query->where('category_id', $currentCategory->id);
            }
        }
        $items = $query->orderByDesc('sort')->orderByDesc('id')->paginate(12)->through(function ($item) {
            return [
                'title' => $item->title,
                'slug' => $item->slug,
                'cover' => $item->cover ?: '/static/common/images/logo-1.png',
                'summary' => $item->summary,
                'model_no' => $item->model_no,
                'url' => route('website.products.show', ['slug' => $item->slug ?: $item->id]),
            ];
        });

        return [
            'categories' => $categories->map(fn ($item) => [
                'title' => $item->title,
                'slug' => $item->slug,
                'url' => route('website.products.index', ['category' => $item->slug]),
            ])->values()->all(),
            'currentCategory' => $currentCategory ? ['title' => $currentCategory->title, 'slug' => $currentCategory->slug] : null,
            'items' => $items,
            'banner' => $this->pageBanner('products', '产品中心'),
        ];
    }

    public function productDetail(string $slug): ?array
    {
        if (!Schema::hasTable('website_product')) {
            return null;
        }

        $item = WebsiteProduct::query()->where('status', 1)->where('slug', $slug)->first();
        if (!$item) {
            return null;
        }

        $gallery = collect(preg_split('/[|,
]+/', (string) $item->gallery, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($src) => trim($src))
            ->filter()
            ->values()
            ->all();
        if (empty($gallery) && $item->cover) {
            $gallery = [$item->cover];
        }

        $specs = collect(preg_split('/
||
/', (string) $item->parameters, -1, PREG_SPLIT_NO_EMPTY))
            ->map(function ($line) {
                [$label, $value] = array_pad(explode(':', $line, 2), 2, '');
                if (empty($value)) {
                    [$label, $value] = array_pad(explode('：', $line, 2), 2, '');
                }
                return ['label' => trim($label), 'value' => trim($value)];
            })
            ->filter(fn ($row) => $row['label'] !== '' || $row['value'] !== '')
            ->values()
            ->all();

        $related = WebsiteProduct::query()
            ->where('status', 1)
            ->where('id', '<>', $item->id)
            ->where('category_id', $item->category_id)
            ->orderByDesc('sort')
            ->limit(4)
            ->get(['title', 'slug', 'cover'])
            ->map(fn ($row) => [
                'title' => $row->title,
                'cover' => $row->cover ?: '/static/common/images/logo-1.png',
                'url' => route('website.products.show', ['slug' => $row->slug ?: $row->id]),
            ])
            ->all();

        $category = null;
        if (Schema::hasTable('website_product_category') && $item->category_id) {
            $categoryModel = WebsiteProductCategory::find($item->category_id);
            if ($categoryModel) {
                $category = [
                    'title' => $categoryModel->title,
                    'url' => route('website.products.index', ['category' => $categoryModel->slug]),
                ];
            }
        }

        return [
            'title' => $item->title,
            'summary' => $item->summary,
            'content' => $item->content,
            'cover' => $item->cover ?: '/static/common/images/logo-1.png',
            'gallery' => $gallery,
            'specs' => $specs,
            'model_no' => $item->model_no,
            'download_url' => $item->download_url,
            'category' => $category,
            'seo_title' => $item->seo_title,
            'seo_description' => $item->seo_description,
            'seo_keywords' => $item->seo_keywords,
            'related' => $related,
            'banner' => $this->pageBanner('products', '产品详情'),
        ];
    }

    public function articleList(string $categorySlug): array
    {
        $category = $this->articleCategoryBySlug($categorySlug);
        $query = WebsiteArticle::query()->where('status', 1);
        if ($category) {
            $query->where('category_id', $category->id);
        }
        $items = $query->orderByDesc('is_recommend')->orderByDesc('published_at')->orderByDesc('id')->paginate(10)->through(function ($item) {
            return [
                'title' => $item->title,
                'slug' => $item->slug,
                'cover' => $item->cover ?: '/static/common/images/logo-1.png',
                'summary' => $item->summary,
                'published_at' => $item->published_at ? date('Y-m-d', (int) $item->published_at) : date('Y-m-d'),
                'url' => route('website.articles.show', ['slug' => $item->slug ?: $item->id]),
            ];
        });

        $categories = Schema::hasTable('website_article_category')
            ? WebsiteArticleCategory::query()->where('status', 1)->orderByDesc('sort')->orderBy('id')->get(['title', 'slug'])
                ->map(fn ($row) => [
                    'title' => $row->title,
                    'slug' => $row->slug,
                    'url' => $this->articleCategoryUrl($row->slug),
                ])->values()->all()
            : [];

        return [
            'category' => $category ? ['title' => $category->title, 'slug' => $category->slug, 'summary' => $category->summary] : null,
            'categories' => $categories,
            'items' => $items,
            'banner' => $this->pageBanner($categorySlug, $category?->title ?: '资讯列表'),
        ];
    }

    public function articleDetail(string $slug): ?array
    {
        if (!Schema::hasTable('website_article')) {
            return null;
        }
        $item = WebsiteArticle::query()->where('status', 1)->where('slug', $slug)->first();
        if (!$item) {
            return null;
        }

        $category = null;
        $categorySlug = 'news';
        if (Schema::hasTable('website_article_category') && $item->category_id) {
            $categoryModel = WebsiteArticleCategory::find($item->category_id);
            if ($categoryModel) {
                $categorySlug = $categoryModel->slug ?: 'news';
                $category = [
                    'title' => $categoryModel->title,
                    'url' => $this->articleCategoryUrl($categorySlug),
                ];
            }
        }

        $prev = WebsiteArticle::query()->where('status', 1)->where('id', '<', $item->id)->orderByDesc('id')->first(['title', 'slug']);
        $next = WebsiteArticle::query()->where('status', 1)->where('id', '>', $item->id)->orderBy('id')->first(['title', 'slug']);
        $related = WebsiteArticle::query()->where('status', 1)->where('id', '<>', $item->id)->where('category_id', $item->category_id)->orderByDesc('published_at')->limit(4)->get(['title', 'slug'])->map(fn ($row) => [
            'title' => $row->title,
            'url' => route('website.articles.show', ['slug' => $row->slug ?: $row->id]),
        ])->all();

        return [
            'title' => $item->title,
            'summary' => $item->summary,
            'content' => $item->content,
            'cover' => $item->cover ?: '/static/common/images/logo-1.png',
            'author' => $item->author,
            'source' => $item->source,
            'published_at' => $item->published_at ? date('Y-m-d', (int) $item->published_at) : date('Y-m-d'),
            'category' => $category,
            'category_slug' => $categorySlug,
            'seo_title' => $item->seo_title,
            'seo_description' => $item->seo_description,
            'seo_keywords' => $item->seo_keywords,
            'prev' => $prev ? ['title' => $prev->title, 'url' => route('website.articles.show', ['slug' => $prev->slug ?: $prev->id])] : null,
            'next' => $next ? ['title' => $next->title, 'url' => route('website.articles.show', ['slug' => $next->slug ?: $next->id])] : null,
            'related' => $related,
            'banner' => $this->pageBanner($categorySlug, '文章详情'),
        ];
    }

    public function latestArticles(string $categorySlug = 'news', int $limit = 4): array
    {
        if (!Schema::hasTable('website_article')) {
            return [];
        }
        $query = WebsiteArticle::query()->where('status', 1);
        $category = $this->articleCategoryBySlug($categorySlug);
        if ($category) {
            $query->where('category_id', $category->id);
        }
        return $query->orderByDesc('is_recommend')->orderByDesc('published_at')->limit($limit)->get(['title', 'slug', 'summary', 'cover', 'published_at'])->map(fn ($item) => [
            'title' => $item->title,
            'summary' => $item->summary,
            'cover' => $item->cover ?: '/static/common/images/logo-1.png',
            'published_at' => $item->published_at ? date('Y-m-d', (int) $item->published_at) : date('Y-m-d'),
            'url' => route('website.articles.show', ['slug' => $item->slug ?: $item->id]),
        ])->all();
    }

    public function videoList(): array
    {
        $items = Schema::hasTable('website_video')
            ? WebsiteVideo::query()->where('status', 1)->orderByDesc('is_featured')->orderByDesc('sort')->orderByDesc('id')->paginate(12)->through(function ($item) {
                return [
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'summary' => $item->summary,
                    'cover' => $item->cover ?: '/static/common/images/logo-1.png',
                    'url' => route('website.videos.show', ['slug' => $item->slug ?: $item->id]),
                ];
            })
            : collect();

        return [
            'items' => $items,
            'banner' => $this->pageBanner('videos', '视频中心'),
        ];
    }

    public function featuredVideos(int $limit = 4): array
    {
        if (!Schema::hasTable('website_video')) {
            return [];
        }
        return WebsiteVideo::query()->where('status', 1)->orderByDesc('is_featured')->orderByDesc('sort')->limit($limit)->get(['title', 'slug', 'summary', 'cover'])->map(fn ($item) => [
            'title' => $item->title,
            'summary' => $item->summary,
            'cover' => $item->cover ?: '/static/common/images/logo-1.png',
            'url' => route('website.videos.show', ['slug' => $item->slug ?: $item->id]),
        ])->all();
    }

    public function videoDetail(string $slug): ?array
    {
        if (!Schema::hasTable('website_video')) {
            return null;
        }
        $item = WebsiteVideo::query()->where('status', 1)->where('slug', $slug)->first();
        if (!$item) {
            return null;
        }
        $related = WebsiteVideo::query()->where('status', 1)->where('id', '<>', $item->id)->orderByDesc('is_featured')->limit(4)->get(['title', 'slug', 'cover'])->map(fn ($row) => [
            'title' => $row->title,
            'cover' => $row->cover ?: '/static/common/images/logo-1.png',
            'url' => route('website.videos.show', ['slug' => $row->slug ?: $row->id]),
        ])->all();

        return [
            'title' => $item->title,
            'summary' => $item->summary,
            'content' => $item->content,
            'cover' => $item->cover ?: '/static/common/images/logo-1.png',
            'video_url' => $item->video_url,
            'seo_title' => $item->seo_title,
            'seo_description' => $item->seo_description,
            'seo_keywords' => $item->seo_keywords,
            'related' => $related,
            'banner' => $this->pageBanner('videos', '视频详情'),
        ];
    }

    public function pageBySlug(string $slug): ?array
    {
        if (!Schema::hasTable('website_channel')) {
            return null;
        }
        $page = WebsiteChannel::query()->where('status', 1)->where('slug', $slug)->where('type', 'single')->first();
        if (!$page) {
            return null;
        }
        return [
            'title' => $page->title,
            'summary' => $page->summary,
            'content' => $page->content,
            'cover' => $page->cover ?: '/static/common/images/logo-1.png',
            'banner' => $page->banner ?: ($page->cover ?: '/static/common/images/logo-1.png'),
            'seo_title' => $page->seo_title,
            'seo_description' => $page->seo_description,
            'seo_keywords' => $page->seo_keywords,
        ];
    }

    public function search(?string $keyword): array
    {
        $keyword = trim((string) $keyword);
        if ($keyword === '') {
            return ['keyword' => '', 'products' => [], 'articles' => [], 'videos' => []];
        }
        $products = Schema::hasTable('website_product') ? WebsiteProduct::query()->where('status', 1)->where(function ($query) use ($keyword) {
            $query->where('title', 'like', "%{$keyword}%")->orWhere('summary', 'like', "%{$keyword}%")->orWhere('content', 'like', "%{$keyword}%");
        })->limit(8)->get(['title', 'slug', 'summary'])->map(fn ($item) => [
            'title' => $item->title,
            'summary' => $item->summary,
            'url' => route('website.products.show', ['slug' => $item->slug ?: $item->id]),
            'type' => '产品',
        ])->all() : [];

        $articles = Schema::hasTable('website_article') ? WebsiteArticle::query()->where('status', 1)->where(function ($query) use ($keyword) {
            $query->where('title', 'like', "%{$keyword}%")->orWhere('summary', 'like', "%{$keyword}%")->orWhere('content', 'like', "%{$keyword}%");
        })->limit(8)->get(['title', 'slug', 'summary'])->map(fn ($item) => [
            'title' => $item->title,
            'summary' => $item->summary,
            'url' => route('website.articles.show', ['slug' => $item->slug ?: $item->id]),
            'type' => '资讯',
        ])->all() : [];

        $videos = Schema::hasTable('website_video') ? WebsiteVideo::query()->where('status', 1)->where(function ($query) use ($keyword) {
            $query->where('title', 'like', "%{$keyword}%")->orWhere('summary', 'like', "%{$keyword}%")->orWhere('content', 'like', "%{$keyword}%");
        })->limit(8)->get(['title', 'slug', 'summary'])->map(fn ($item) => [
            'title' => $item->title,
            'summary' => $item->summary,
            'url' => route('website.videos.show', ['slug' => $item->slug ?: $item->id]),
            'type' => '视频',
        ])->all() : [];

        return compact('keyword', 'products', 'articles', 'videos');
    }

    public function links(): Collection
    {
        if (!Schema::hasTable('website_link')) {
            return collect();
        }
        return WebsiteLink::query()->where('status', 1)->orderByDesc('sort')->orderBy('id')->get(['title', 'url', 'logo'])->map(fn ($item) => [
            'title' => $item->title,
            'url' => $item->url ?: 'javascript:;',
            'logo' => $item->logo ?: '/static/common/images/logo-1.png',
        ]);
    }

    protected function productItems(string $flagField, int $limit = 8): array
    {
        if (!Schema::hasTable('website_product')) {
            return [];
        }

        $query = WebsiteProduct::query()->where('status', 1);
        if (Schema::hasColumn('website_product', $flagField)) {
            $query->where($flagField, 1);
        }

        return $query->orderByDesc('sort')->orderByDesc('id')->limit($limit)->get(['title', 'slug', 'summary', 'cover', 'model_no'])->map(fn ($item) => [
            'title' => $item->title,
            'summary' => $item->summary ?: 'Product summary',
            'cover' => $item->cover ?: '/static/common/images/logo-1.png',
            'model_no' => $item->model_no,
            'url' => route('website.products.show', ['slug' => $item->slug ?: $item->id]),
        ])->all();
    }

    protected function articleCategoryBySlug(string $slug): ?WebsiteArticleCategory
    {
        if (!Schema::hasTable('website_article_category')) {
            return null;
        }
        return WebsiteArticleCategory::query()->where('status', 1)->where('slug', $slug)->first();
    }

    protected function articleCategoryUrl(string $slug): string
    {
        return match ($slug) {
            'support' => route('website.support.index'),
            'literature' => route('website.literature.index'),
            default => route('website.articles.index'),
        };
    }

    protected function pageBanner(string $slug, string $title): array
    {
        $page = Schema::hasTable('website_channel')
            ? WebsiteChannel::query()->where('status', 1)->where('slug', $slug)->first(['title', 'summary', 'banner', 'cover'])
            : null;

        return [
            'title' => $page?->title ?: $title,
            'summary' => $page?->summary ?: ($this->site()['tagline'] ?? ''),
            'image' => $page?->banner ?: ($page?->cover ?: '/static/common/images/logo-1.png'),
        ];
    }

    protected function channelUrl(?string $type, ?string $slug): string
    {
        return match ($slug) {
            'about-us' => route('website.about'),
            'agents' => route('website.agents'),
            'support' => route('website.support.index'),
            'literature' => route('website.literature.index'),
            'news' => route('website.articles.index'),
            'videos' => route('website.videos.index'),
            'products' => route('website.products.index'),
            default => match ($type) {
                'product' => route('website.products.index', array_filter(['category' => $slug])),
                'article' => route('website.articles.index'),
                'video' => route('website.videos.index'),
                'single' => route('website.page.show', ['slug' => $slug ?: 'page']),
                'link' => $slug ?: 'javascript:;',
                default => route('website.home'),
            },
        };
    }

    protected function config(string $group, string $name, mixed $default = null): mixed
    {
        $value = sysconfig($group, $name);
        return ($value === null || $value === '') ? $default : $value;
    }
}
