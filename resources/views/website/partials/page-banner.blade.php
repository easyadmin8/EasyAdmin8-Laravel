<section class="page-banner" style="background-image:linear-gradient(rgba(10,52,88,.78),rgba(10,52,88,.62)), url('{{ $banner['image'] ?? '/static/common/images/logo-1.png' }}')">
    <div class="site-container page-banner__inner">
        <div>
            <p class="page-banner__eyebrow">{{ $eyebrow ?? 'Website' }}</p>
            <h1>{{ $banner['title'] ?? '' }}</h1>
            @if(!empty($banner['summary'] ?? ''))
                <p class="page-banner__summary">{{ $banner['summary'] }}</p>
            @endif
        </div>
    </div>
</section>
