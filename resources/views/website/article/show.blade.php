@extends('website.layouts.app')
@section('content')
    @include('website.partials.page-banner', ['banner' => $detail['banner'], 'eyebrow' => 'Article Detail'])
    <section class="site-section page-section">
        <div class="site-container article-detail">
            <div class="article-detail__main">
                <span class="detail-badge">{{ $detail['category']['title'] ?? 'Article' }}</span>
                <h1>{{ $detail['title'] }}</h1>
                <div class="article-detail__meta"><span>{{ $detail['published_at'] }}</span>@if(!empty($detail['author']))<span>Author: {{ $detail['author'] }}</span>@endif @if(!empty($detail['source']))<span>Source: {{ $detail['source'] }}</span>@endif</div>
                @if(!empty($detail['cover']))<img src="{{ $detail['cover'] }}" alt="{{ $detail['title'] }}" class="article-detail__cover">@endif
                <div class="detail-richtext">{!! $detail['content'] !!}</div>
                <div class="article-detail__pager">
                    <div>@if($detail['prev'])<span>上一篇</span><a href="{{ $detail['prev']['url'] }}">{{ $detail['prev']['title'] }}</a>@endif</div>
                    <div>@if($detail['next'])<span>下一篇</span><a href="{{ $detail['next']['url'] }}">{{ $detail['next']['title'] }}</a>@endif</div>
                </div>
            </div>
            <aside class="article-detail__side">
                <div class="side-panel">
                    <h3>相关文章</h3>
                    @foreach(($detail['related'] ?? []) as $item)
                        <a href="{{ $item['url'] }}" class="side-link">{{ $item['title'] }}</a>
                    @endforeach
                </div>
            </aside>
        </div>
    </section>
@endsection
