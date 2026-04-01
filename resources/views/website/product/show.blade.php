@extends('website.layouts.app')
@section('content')
    @include('website.partials.page-banner', ['banner' => $detail['banner'], 'eyebrow' => 'Product Detail'])
    <section class="site-section page-section">
        <div class="site-container detail-layout">
            <div class="detail-gallery">
                <img src="{{ $detail['gallery'][0] ?? $detail['cover'] }}" alt="{{ $detail['title'] }}" class="detail-gallery__main">
                @if(count($detail['gallery']) > 1)
                    <div class="detail-gallery__thumbs">
                        @foreach($detail['gallery'] as $image)
                            <img src="{{ $image }}" alt="{{ $detail['title'] }}">
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="detail-content">
                <span class="detail-badge">{{ $detail['category']['title'] ?? 'Product' }}</span>
                <h1>{{ $detail['title'] }}</h1>
                <p class="detail-summary">{{ $detail['summary'] }}</p>
                @if(!empty($detail['model_no']))<p class="detail-meta"><strong>Model:</strong> {{ $detail['model_no'] }}</p>@endif
                @if(!empty($detail['download_url']))<a class="site-button site-button--primary" href="{{ $detail['download_url'] }}">Download brochure</a>@endif
                @if(!empty($detail['specs']))
                    <div class="detail-specs">
                        @foreach($detail['specs'] as $row)
                            <div class="detail-specs__item"><span>{{ $row['label'] }}</span><strong>{{ $row['value'] }}</strong></div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="site-container detail-richtext">{!! $detail['content'] !!}</div>
        @if(!empty($detail['related']))
            <div class="site-container related-section">
                <div class="section-lead"><h2>相关产品</h2></div>
                <div class="product-grid product-grid--compact">
                    @foreach($detail['related'] as $product)
                        <article class="product-card product-card--compact"><a href="{{ $product['url'] }}" class="product-card__image-wrap"><img src="{{ $product['cover'] }}" alt="{{ $product['title'] }}" class="product-card__image"></a><div class="product-card__body"><h3><a href="{{ $product['url'] }}">{{ $product['title'] }}</a></h3></div></article>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
