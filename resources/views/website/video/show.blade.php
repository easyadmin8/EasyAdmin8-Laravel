@extends('website.layouts.app')
@section('content')
    @include('website.partials.page-banner', ['banner' => $detail['banner'], 'eyebrow' => 'Video Detail'])
    <section class="site-section page-section">
        <div class="site-container article-detail">
            <div class="article-detail__main">
                <span class="detail-badge">Video</span>
                <h1>{{ $detail['title'] }}</h1>
                <p class="detail-summary">{{ $detail['summary'] }}</p>
                <div class="video-player">
                    @if(!empty($detail['video_url']))
                        @if(Str::contains($detail['video_url'], ['youtube.com', 'youtu.be', 'bilibili.com']))
                            <iframe src="{{ $detail['video_url'] }}" title="{{ $detail['title'] }}" allowfullscreen></iframe>
                        @else
                            <video controls poster="{{ $detail['cover'] }}"><source src="{{ $detail['video_url'] }}"></video>
                        @endif
                    @else
                        <img src="{{ $detail['cover'] }}" alt="{{ $detail['title'] }}" class="article-detail__cover">
                    @endif
                </div>
                <div class="detail-richtext">{!! $detail['content'] !!}</div>
            </div>
            <aside class="article-detail__side">
                <div class="side-panel">
                    <h3>相关视频</h3>
                    @foreach(($detail['related'] ?? []) as $item)
                        <a href="{{ $item['url'] }}" class="side-link">{{ $item['title'] }}</a>
                    @endforeach
                </div>
            </aside>
        </div>
    </section>
@endsection
