@extends('website.layouts.app')
@section('content')
    @include('website.partials.page-banner', ['banner' => $listing['banner'], 'eyebrow' => 'Articles'])
    <section class="site-section page-section">
        <div class="site-container page-grid">
            <aside class="page-aside">
                <div class="side-panel">
                    <h3>文章分类</h3>
                    @foreach(($listing['categories'] ?? []) as $category)
                        <a href="{{ $category['url'] }}" class="side-link {{ ($listing['category']['slug'] ?? '') === $category['slug'] ? 'is-active' : '' }}">{{ $category['title'] }}</a>
                    @endforeach
                </div>
            </aside>
            <div class="page-content">
                <div class="section-lead"><h2>{{ $listing['category']['title'] ?? '资讯列表' }}</h2><p>{{ $listing['category']['summary'] ?? ($listing['banner']['summary'] ?? '') }}</p></div>
                <div class="article-list">
                    @foreach($listing['items'] as $article)
                        <article class="article-list__item">
                            <a href="{{ $article['url'] }}" class="article-list__cover"><img src="{{ $article['cover'] }}" alt="{{ $article['title'] }}"></a>
                            <div class="article-list__body"><span class="news-card__date">{{ $article['published_at'] }}</span><h3><a href="{{ $article['url'] }}">{{ $article['title'] }}</a></h3><p>{{ $article['summary'] }}</p></div>
                        </article>
                    @endforeach
                </div>
                <div class="website-pagination">{{ $listing['items']->withQueryString()->links() }}</div>
            </div>
        </div>
    </section>
@endsection
