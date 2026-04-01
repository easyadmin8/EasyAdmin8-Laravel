<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = time();

        if (Schema::hasTable('system_config')) {
            $configs = [
                ['name' => 'brand_name', 'group' => 'website', 'value' => 'FUDA CELL', 'remark' => '品牌名'],
                ['name' => 'tagline', 'group' => 'website', 'value' => 'Professional battery and digital accessory manufacturer', 'remark' => '站点标语'],
                ['name' => 'logo', 'group' => 'website', 'value' => '/static/common/images/logo-1.png', 'remark' => '前台LOGO'],
                ['name' => 'service_phone', 'group' => 'website', 'value' => '+86 400-888-6868', 'remark' => '联系电话'],
                ['name' => 'service_email', 'group' => 'website', 'value' => 'sales@fudacell.com', 'remark' => '联系邮箱'],
                ['name' => 'company_address', 'group' => 'website', 'value' => 'Shenzhen, Guangdong, China', 'remark' => '联系地址'],
                ['name' => 'work_time', 'group' => 'website', 'value' => 'Mon - Sat 09:00 - 18:00', 'remark' => '工作时间'],
                ['name' => 'search_tip', 'group' => 'website', 'value' => 'Search products, support, literature and news', 'remark' => '搜索提示'],
                ['name' => 'hero_title', 'group' => 'website', 'value' => 'Professional battery solutions for mobile digital products', 'remark' => '首页标题'],
                ['name' => 'hero_summary', 'group' => 'website', 'value' => 'Focus on consumer batteries, replacement power solutions and digital accessories. Frontend pages are now connected to backend-managed content tables.', 'remark' => '首页摘要'],
                ['name' => 'seo_title', 'group' => 'website', 'value' => 'FUDA CELL', 'remark' => 'SEO标题'],
                ['name' => 'seo_keywords', 'group' => 'website', 'value' => 'battery, accessories, oem, odm', 'remark' => 'SEO关键词'],
                ['name' => 'seo_description', 'group' => 'website', 'value' => 'FUDA CELL professional battery and digital accessories website.', 'remark' => 'SEO描述'],
                ['name' => 'footer_intro', 'group' => 'website_footer', 'value' => 'A modern energy solution supplier focusing on stable quality, agile delivery and long-term cooperation.', 'remark' => '页脚简介'],
                ['name' => 'contact_tip', 'group' => 'website_footer', 'value' => 'Please contact us for OEM, ODM and distributor cooperation.', 'remark' => '联系提示'],
                ['name' => 'agent_phone', 'group' => 'website_footer', 'value' => '+86 755-8888-0000', 'remark' => '代理联系电话'],
                ['name' => 'agent_email', 'group' => 'website_footer', 'value' => 'agent@fudacell.com', 'remark' => '代理邮箱'],
            ];
            foreach ($configs as $index => $config) {
                if (!DB::table('system_config')->where(['group' => $config['group'], 'name' => $config['name']])->exists()) {
                    DB::table('system_config')->insert($config + ['sort' => $index, 'create_time' => $now, 'update_time' => $now]);
                }
            }
        }

        if (Schema::hasTable('system_menu')) {
            $parentId = DB::table('system_menu')->where('href', 'website/config/index')->value('pid');
            if (!$parentId) {
                $topId = DB::table('system_menu')->where('title', '官网管理')->value('id');
                if (!$topId) {
                    $topId = DB::table('system_menu')->insertGetId([
                        'pid' => 0,
                        'title' => '官网管理',
                        'icon' => 'fa fa-globe',
                        'href' => '',
                        'params' => '',
                        'target' => '_self',
                        'sort' => 5,
                        'status' => 1,
                        'remark' => '官网业务模块',
                        'create_time' => $now,
                        'update_time' => $now,
                    ]);
                }
                $menus = [
                    ['title' => '站点配置', 'href' => 'website/config/index'],
                    ['title' => '导航管理', 'href' => 'website/channel/index'],
                    ['title' => 'Banner 管理', 'href' => 'website/banner/index'],
                    ['title' => '热搜关键词', 'href' => 'website/hot_keyword/index'],
                    ['title' => '产品分类', 'href' => 'website/product_category/index'],
                    ['title' => '产品管理', 'href' => 'website/product/index'],
                    ['title' => '文章分类', 'href' => 'website/article_category/index'],
                    ['title' => '文章管理', 'href' => 'website/article/index'],
                    ['title' => '视频资料', 'href' => 'website/video/index'],
                    ['title' => '友情链接', 'href' => 'website/link/index'],
                ];
                foreach ($menus as $sort => $menu) {
                    if (!DB::table('system_menu')->where('href', $menu['href'])->exists()) {
                        DB::table('system_menu')->insert([
                            'pid' => $topId,
                            'title' => $menu['title'],
                            'icon' => 'fa fa-list',
                            'href' => $menu['href'],
                            'params' => '',
                            'target' => '_self',
                            'sort' => 100 - $sort,
                            'status' => 1,
                            'remark' => '官网管理',
                            'create_time' => $now,
                            'update_time' => $now,
                        ]);
                    }
                }
            }
        }

        if (Schema::hasTable('website_channel') && !DB::table('website_channel')->exists()) {
            DB::table('website_channel')->insert([
                ['pid' => 0, 'title' => '关于我们', 'slug' => 'about-us', 'type' => 'single', 'summary' => '企业介绍与发展历程', 'content' => '<p>这里是关于我们页面的默认内容，可在后台导航管理中编辑。</p><p>建议替换为企业简介、资质、工厂图片、发展历程等内容。</p>', 'is_nav' => 1, 'sort' => 90, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
                ['pid' => 0, 'title' => '产品中心', 'slug' => 'products', 'type' => 'product', 'summary' => '产品中心列表页的栏目说明', 'is_nav' => 1, 'sort' => 89, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
                ['pid' => 0, 'title' => '技术支持', 'slug' => 'support', 'type' => 'article', 'summary' => '技术支持文章列表与资料说明', 'is_nav' => 1, 'sort' => 88, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
                ['pid' => 0, 'title' => '文献引用', 'slug' => 'literature', 'type' => 'article', 'summary' => '文献引用与知识资料列表', 'is_nav' => 1, 'sort' => 87, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
                ['pid' => 0, 'title' => '新闻资讯', 'slug' => 'news', 'type' => 'article', 'summary' => '企业新闻与行业动态', 'is_nav' => 1, 'sort' => 86, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
                ['pid' => 0, 'title' => '视频中心', 'slug' => 'videos', 'type' => 'video', 'summary' => '视频资料列表页面', 'is_nav' => 1, 'sort' => 85, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
                ['pid' => 0, 'title' => '全国代理', 'slug' => 'agents', 'type' => 'single', 'summary' => '全国代理招募与联系信息', 'content' => '<p>这里是全国代理页面的默认内容，可在后台导航管理中编辑。</p><p>建议替换为代理政策、区域联系方式、合作流程等内容。</p>', 'is_nav' => 1, 'sort' => 84, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
            ]);
        }

        if (Schema::hasTable('website_article_category') && !DB::table('website_article_category')->exists()) {
            DB::table('website_article_category')->insert([
                ['title' => '新闻资讯', 'slug' => 'news', 'summary' => '企业新闻与行业资讯', 'sort' => 90, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
                ['title' => '技术支持', 'slug' => 'support', 'summary' => '技术支持资料与常见问题', 'sort' => 89, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
                ['title' => '文献引用', 'slug' => 'literature', 'summary' => '文献引用与知识资料', 'sort' => 88, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
            ]);
        }

        if (Schema::hasTable('website_banner') && !DB::table('website_banner')->exists()) {
            DB::table('website_banner')->insert([
                'title' => 'Factory direct digital power solutions',
                'subtitle' => 'Fast sampling · Stable quality · Flexible customization',
                'image' => '/static/common/images/logo-1.png',
                'link' => '/products',
                'button_text' => 'Browse products',
                'target' => '_self',
                'sort' => 100,
                'status' => 1,
                'create_time' => $now,
                'update_time' => $now,
            ]);
        }

        if (Schema::hasTable('website_hot_keyword') && !DB::table('website_hot_keyword')->exists()) {
            foreach (['Li-ion battery', 'Phone battery', 'OEM', 'ODM', 'Support document', 'Video center'] as $index => $keyword) {
                DB::table('website_hot_keyword')->insert([
                    'keyword' => $keyword,
                    'link' => '/search?keyword=' . urlencode($keyword),
                    'sort' => 100 - $index,
                    'status' => 1,
                    'create_time' => $now,
                    'update_time' => $now,
                ]);
            }
        }

        if (Schema::hasTable('website_product_category') && !DB::table('website_product_category')->exists()) {
            DB::table('website_product_category')->insert([
                ['title' => 'Lithium Battery', 'slug' => 'lithium-battery', 'summary' => 'Portable and reliable energy storage solutions.', 'cover' => '/static/common/images/logo-1.png', 'is_featured' => 1, 'sort' => 100, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
                ['title' => 'Phone Battery', 'slug' => 'phone-battery', 'summary' => 'Replacement battery models for hot devices.', 'cover' => '/static/common/images/logo-1.png', 'is_featured' => 1, 'sort' => 99, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
                ['title' => 'Digital Accessories', 'slug' => 'digital-accessories', 'summary' => 'Cables, chargers and related accessories.', 'cover' => '/static/common/images/logo-1.png', 'is_featured' => 1, 'sort' => 98, 'status' => 1, 'create_time' => $now, 'update_time' => $now],
            ]);
        }

        if (Schema::hasTable('website_product') && !DB::table('website_product')->exists()) {
            $categoryId = DB::table('website_product_category')->orderBy('id')->value('id') ?: 0;
            foreach ([
                ['title' => 'High Capacity Phone Battery', 'slug' => 'high-capacity-phone-battery', 'model_no' => 'FC-1001', 'summary' => 'Stable voltage platform and long cycle life.', 'is_featured' => 1, 'is_new' => 1],
                ['title' => 'Fast Charge Power Pack', 'slug' => 'fast-charge-power-pack', 'model_no' => 'FC-1002', 'summary' => 'Compact energy module for consumer electronics.', 'is_featured' => 1, 'is_new' => 0],
                ['title' => 'OEM Battery Module', 'slug' => 'oem-battery-module', 'model_no' => 'FC-1003', 'summary' => 'Flexible customization for export clients.', 'is_featured' => 0, 'is_new' => 1],
            ] as $index => $item) {
                DB::table('website_product')->insert($item + [
                    'category_id' => $categoryId,
                    'cover' => '/static/common/images/logo-1.png',
                    'gallery' => '/static/common/images/logo-1.png|/static/common/images/logo-1.png',
                    'content' => '<p>This is sample product content seeded by migration. Replace it in the backend product manager.</p>',
                    'parameters' => "Capacity: 5000mAh
Voltage: 3.7V
Cycle life: 500+",
                    'download_url' => '',
                    'sort' => 100 - $index,
                    'status' => 1,
                    'create_time' => $now,
                    'update_time' => $now,
                ]);
            }
        }

        if (Schema::hasTable('website_article') && !DB::table('website_article')->exists()) {
            $newsId = DB::table('website_article_category')->where('slug', 'news')->value('id') ?: 0;
            $supportId = DB::table('website_article_category')->where('slug', 'support')->value('id') ?: 0;
            $literatureId = DB::table('website_article_category')->where('slug', 'literature')->value('id') ?: 0;
            $articles = [
                ['category_id' => $newsId, 'title' => 'How battery customization projects are delivered faster', 'slug' => 'battery-customization-projects', 'summary' => 'A seeded example news article, editable from the backend.', 'content' => '<p>This is a seeded example news article. Replace it in the backend article manager.</p>', 'published_at' => $now, 'is_recommend' => 1],
                ['category_id' => $supportId, 'title' => 'Battery safety use guide', 'slug' => 'battery-safety-use-guide', 'summary' => 'A seeded technical support article.', 'content' => '<p>This is a seeded support article. Replace it in the backend article manager.</p>', 'published_at' => $now - 86400, 'is_recommend' => 1],
                ['category_id' => $literatureId, 'title' => 'Literature citation sample', 'slug' => 'literature-citation-sample', 'summary' => 'A seeded literature article.', 'content' => '<p>This is a seeded literature article. Replace it in the backend article manager.</p>', 'published_at' => $now - 172800, 'is_recommend' => 1],
            ];
            foreach ($articles as $index => $item) {
                DB::table('website_article')->insert($item + [
                    'cover' => '/static/common/images/logo-1.png',
                    'author' => 'Admin',
                    'source' => 'FUDA CELL',
                    'sort' => 100 - $index,
                    'status' => 1,
                    'create_time' => $now,
                    'update_time' => $now,
                ]);
            }
        }

        if (Schema::hasTable('website_video') && !DB::table('website_video')->exists()) {
            foreach ([
                ['title' => 'Factory Walkthrough', 'slug' => 'factory-walkthrough', 'summary' => 'Production line and QC workflow overview.'],
                ['title' => 'Product Assembly Demo', 'slug' => 'product-assembly-demo', 'summary' => 'Structure, packing and delivery readiness.'],
            ] as $index => $item) {
                DB::table('website_video')->insert($item + [
                    'cover' => '/static/common/images/logo-1.png',
                    'content' => '<p>This is seeded video detail content. Replace it in the backend video manager.</p>',
                    'video_url' => '',
                    'is_featured' => 1,
                    'sort' => 100 - $index,
                    'status' => 1,
                    'create_time' => $now,
                    'update_time' => $now,
                ]);
            }
        }

        if (Schema::hasTable('website_link') && !DB::table('website_link')->exists()) {
            foreach (['Global Sources', 'Made-in-China', 'Industry Partner', 'Battery Alliance'] as $index => $title) {
                DB::table('website_link')->insert([
                    'title' => $title,
                    'logo' => '/static/common/images/logo-1.png',
                    'url' => 'javascript:;',
                    'target' => '_blank',
                    'sort' => 100 - $index,
                    'status' => 1,
                    'create_time' => $now,
                    'update_time' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('system_menu')) {
            DB::table('system_menu')->whereIn('href', [
                'website/config/index', 'website/channel/index', 'website/banner/index', 'website/hot_keyword/index', 'website/product_category/index',
                'website/product/index', 'website/article_category/index', 'website/article/index', 'website/video/index', 'website/link/index'
            ])->delete();
            DB::table('system_menu')->where('title', '官网管理')->delete();
        }
    }
};
