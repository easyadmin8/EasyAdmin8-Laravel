@extends('website.layouts.app')
@section('content')
    @include('website.partials.page-banner', ['banner' => ['title' => $page['title'], 'summary' => $page['summary'], 'image' => $page['banner']], 'eyebrow' => 'Company'])
    <section class="site-section page-section">
        <div class="site-container single-page">
            <div class="section-lead"><h2>{{ $page['title'] }}</h2><p>{{ $page['summary'] }}</p></div>
            <div class="detail-richtext">{!! $page['content'] !!}</div>
        </div>
    </section>
@endsection
