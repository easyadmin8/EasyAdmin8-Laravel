@extends('website.layouts.app')

@section('content')
    <section class="placeholder-section">
        <div class="site-container placeholder-section__inner">
            <p class="site-section-heading__eyebrow">Frontend framework ready</p>
            <h1>{{ $headline ?? 'Page placeholder' }}</h1>
            <p>{{ $description ?? '' }}</p>
            @isset($slug)
                <div class="placeholder-section__tag">Current slug: {{ $slug }}</div>
            @endisset
            <div class="placeholder-section__actions">
                <a href="{{ route('website.home') }}" class="site-button site-button--primary">Back to home</a>
                <a href="{{ route('website.products.index') }}" class="site-button site-button--ghost">Products</a>
            </div>
        </div>
    </section>
@endsection
