@extends('website.layouts.app')
@section('content')
    @include('website.partials.page-banner', ['banner' => $listing['banner'], 'eyebrow' => 'Products'])
    <section class="site-section page-section">
        <div class="site-container page-grid">
            <aside class="page-aside">
                <div class="side-panel">
                    <h3>产品分类</h3>
                    <a href="{{ route('website.products.index') }}" class="side-link {{ empty($listing['currentCategory']) ? 'is-active' : '' }}">全部产品</a>
                    @foreach(($listing['categories'] ?? []) as $category)
                        <a href="{{ $category['url'] }}" class="side-link {{ ($listing['currentCategory']['slug'] ?? '') === $category['slug'] ? 'is-active' : '' }}">{{ $category['title'] }}</a>
                    @endforeach
                </div>
            </aside>
            <div class="page-content">
                <div class="section-lead"><h2>{{ $listing['currentCategory']['title'] ?? '产品中心' }}</h2><p>{{ $listing['banner']['summary'] ?? '' }}</p></div>
                <div class="product-grid product-grid--listing">
                    @foreach($listing['items'] as $product)
                        <article class="product-card">
                            <a href="{{ $product['url'] }}" class="product-card__image-wrap"><img src="{{ $product['cover'] }}" alt="{{ $product['title'] }}" class="product-card__image"></a>
                            <div class="product-card__body"><h3><a href="{{ $product['url'] }}">{{ $product['title'] }}</a></h3><p>{{ $product['summary'] }}</p>@if(!empty($product['model_no']))<span class="card-tag">{{ $product['model_no'] }}</span>@endif</div>
                        </article>
                    @endforeach
                </div>
                <div class="website-pagination">{{ $listing['items']->withQueryString()->links() }}</div>
            </div>
        </div>
    </section>
@endsection
