@extends('website.layouts.app')
@section('content')
    @include('website.partials.page-banner', ['banner' => $listing['banner'], 'eyebrow' => 'Videos'])
    <section class="site-section page-section">
        <div class="site-container">
            <div class="section-lead"><h2>视频资料</h2><p>{{ $listing['banner']['summary'] ?? '' }}</p></div>
            <div class="video-grid video-grid--listing">
                @foreach($listing['items'] as $video)
                    <article class="video-card"><a href="{{ $video['url'] }}" class="video-card__cover"><img src="{{ $video['cover'] }}" alt="{{ $video['title'] }}"><span class="video-card__play">▶</span></a><div class="video-card__body"><h3><a href="{{ $video['url'] }}">{{ $video['title'] }}</a></h3><p>{{ $video['summary'] }}</p></div></article>
                @endforeach
            </div>
            <div class="website-pagination">{{ $listing['items']->withQueryString()->links() }}</div>
        </div>
    </section>
@endsection
