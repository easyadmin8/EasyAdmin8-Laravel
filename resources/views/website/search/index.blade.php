@extends('website.layouts.app')
@section('content')
    <section class="site-section page-section">
        <div class="site-container search-page">
            <div class="section-lead"><h1>搜索结果</h1><p>关键词：{{ $result['keyword'] ?: '未输入关键词' }}</p></div>
            @foreach(['products' => '产品', 'articles' => '资讯', 'videos' => '视频'] as $key => $label)
                <div class="search-block">
                    <h2>{{ $label }}</h2>
                    @forelse($result[$key] as $item)
                        <a href="{{ $item['url'] }}" class="search-item"><strong>{{ $item['title'] }}</strong><span>{{ $item['summary'] }}</span></a>
                    @empty
                        <p class="search-empty">暂无{{ $label }}结果</p>
                    @endforelse
                </div>
            @endforeach
        </div>
    </section>
@endsection
