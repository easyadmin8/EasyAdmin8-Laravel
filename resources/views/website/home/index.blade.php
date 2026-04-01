@extends('website.layouts.app')

@section('content')
    @php($banner = $home['banners'][0] ?? null)
    <section class="hero-section">
        <div class="site-container hero-section__inner">
            <div class="hero-copy">
                <p class="hero-copy__eyebrow">{{ $site['brand_name'] ?? '' }}</p>
                <h1>{{ $site['hero_title'] ?? '' }}</h1>
                <p class="hero-copy__summary">{{ $site['hero_summary'] ?? '' }}</p>
                <div class="hero-copy__actions">
                    <a href="{{ $banner['link'] ?? route('website.products.index') }}" class="site-button site-button--primary">{{ $banner['button_text'] ?? 'Browse products' }}</a>
                    <a href="{{ route('website.about') }}" class="site-button site-button--ghost">Learn more</a>
                </div>
                <ul class="hero-copy__points">
                    <li>Factory direct</li>
                    <li>OEM / ODM</li>
                    <li>Global delivery</li>
                </ul>
            </div>
            <div class="hero-visual">
                <div class="hero-visual__banner">
                    <div class="hero-visual__badge">Responsive Home Prototype</div>
                    <h2>{{ $banner['title'] ?? '' }}</h2>
                    <p>{{ $banner['subtitle'] ?? '' }}</p>
                </div>
                <div class="hero-visual__cards">
                    @foreach(($home['featuredCategories'] ?? []) as $category)
                        <a href="{{ $category['url'] }}" class="hero-mini-card">
                            <img src="{{ $category['cover'] }}" alt="{{ $category['title'] }}">
                            <strong>{{ $category['title'] }}</strong>
                            <span>{{ $category['summary'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="site-section">
        <div class="site-container">
            <div class="site-section-heading">
                <div>
                    <p class="site-section-heading__eyebrow">Featured Products</p>
                    <h2 class="site-section-heading__title">特色产品</h2>
                </div>
                <a href="{{ route('website.products.index') }}" class="site-section-heading__more">View more</a>
            </div>
            <div class="product-grid">
                @foreach(($home['featuredProducts'] ?? []) as $product)
                    <article class="product-card">
                        <a href="{{ $product['url'] }}" class="product-card__image-wrap"><img src="{{ $product['cover'] }}" alt="{{ $product['title'] }}" class="product-card__image"></a>
                        <div class="product-card__body">
                            <h3><a href="{{ $product['url'] }}">{{ $product['title'] }}</a></h3>
                            <p>{{ $product['summary'] }}</p>
                            @if(!empty($product['model_no'] ?? ''))<span class="card-tag">{{ $product['model_no'] }}</span>@endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="site-section site-section--alt">
        <div class="site-container">
            <div class="site-section-heading">
                <div><p class="site-section-heading__eyebrow">New Arrivals</p><h2 class="site-section-heading__title">上新产品</h2></div>
                <a href="{{ route('website.products.index') }}" class="site-section-heading__more">Latest shelf</a>
            </div>
            <div class="product-grid product-grid--compact">
                @foreach(($home['newProducts'] ?? []) as $product)
                    <article class="product-card product-card--compact">
                        <a href="{{ $product['url'] }}" class="product-card__image-wrap"><img src="{{ $product['cover'] }}" alt="{{ $product['title'] }}" class="product-card__image"></a>
                        <div class="product-card__body">
                            <h3><a href="{{ $product['url'] }}">{{ $product['title'] }}</a></h3>
                            <p>{{ $product['summary'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="site-section">
        <div class="site-container">
            <div class="site-section-heading">
                <div><p class="site-section-heading__eyebrow">Media Center</p><h2 class="site-section-heading__title">视频资料</h2></div>
                <a href="{{ route('website.videos.index') }}" class="site-section-heading__more">All videos</a>
            </div>
            <div class="video-grid">
                @foreach(($home['videos'] ?? []) as $video)
                    <article class="video-card">
                        <a href="{{ $video['url'] }}" class="video-card__cover"><img src="{{ $video['cover'] }}" alt="{{ $video['title'] }}"><span class="video-card__play">▶</span></a>
                        <div class="video-card__body">
                            <h3><a href="{{ $video['url'] }}">{{ $video['title'] }}</a></h3>
                            <p>{{ $video['summary'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="site-section site-section--alt">
        <div class="site-container">
            <div class="site-section-heading">
                <div><p class="site-section-heading__eyebrow">News</p><h2 class="site-section-heading__title">新闻资讯</h2></div>
                <a href="{{ route('website.articles.index') }}" class="site-section-heading__more">More news</a>
            </div>
            <div class="news-grid">
                @foreach(($home['articles'] ?? []) as $article)
                    <article class="news-card">
                        <a href="{{ $article['url'] }}" class="news-card__cover"><img src="{{ $article['cover'] }}" alt="{{ $article['title'] }}"></a>
                        <div class="news-card__body">
                            <span class="news-card__date">{{ $article['published_at'] }}</span>
                            <h3><a href="{{ $article['url'] }}">{{ $article['title'] }}</a></h3>
                            <p>{{ $article['summary'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
