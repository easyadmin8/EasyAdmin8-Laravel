<header class="site-header">
    <div class="site-topbar">
        <div class="site-container site-topbar__inner">
            <div class="site-topbar__text">
                <span class="site-topbar__label">Manufacturer</span>
                <span>{{ $site['tagline'] ?? '' }}</span>
            </div>
            <div class="site-topbar__contact">
                <a class="site-topbar__phone" href="tel:{{ preg_replace('/\\s+/', '', $site['phone'] ?? '') }}">{{ $site['phone'] ?? '' }}</a>
                <span>{{ $site['work_time'] ?? '' }}</span>
            </div>
        </div>
    </div>

    <div class="site-header__main">
        <div class="site-container site-header__inner">
            <a href="{{ route('website.home') }}" class="site-brand" aria-label="{{ $site['brand_name'] ?? '' }}">
                <span class="site-brand__logo-wrap">
                    <img src="{{ $site['logo'] ?? '/static/common/images/logo-1.png' }}" alt="{{ $site['brand_name'] ?? '' }}" class="site-brand__logo">
                </span>
                <span class="site-brand__meta">
                    <strong>{{ $site['brand_name'] ?? '' }}</strong>
                    <small>{{ $site['site_name'] ?? '' }}</small>
                </span>
            </a>

            <div class="site-search site-search--desktop">
                <form action="{{ route('website.search') }}" method="get" class="site-search__form">
                    <input type="text" name="keyword" class="site-search__input" placeholder="{{ $site['search_tip'] ?? 'Search' }}">
                    <button type="submit" class="site-search__button">Search</button>
                </form>
                <div class="site-search__hotwords">
                    <span>Hot:</span>
                    @foreach(($hotWords ?? []) as $word)
                        <a href="{{ $word['url'] }}">{{ $word['title'] }}</a>
                    @endforeach
                </div>
            </div>

            <button type="button" class="site-menu-toggle" aria-expanded="false" aria-controls="site-nav-drawer" data-menu-toggle>
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <div class="site-nav-wrap">
        <div class="site-container site-nav-wrap__inner">
            <nav class="site-nav site-nav--desktop" aria-label="Primary navigation">
                @foreach(($navigation ?? []) as $item)
                    <a href="{{ $item['url'] }}" class="site-nav__link {{ request()->fullUrlIs($item['url']) || request()->url() === $item['url'] ? 'is-active' : '' }}">{{ $item['title'] }}</a>
                @endforeach
            </nav>
        </div>
    </div>

    <div class="site-nav-drawer" id="site-nav-drawer" data-menu-drawer>
        <div class="site-nav-drawer__panel">
            <div class="site-search site-search--mobile">
                <form action="{{ route('website.search') }}" method="get" class="site-search__form">
                    <input type="text" name="keyword" class="site-search__input" placeholder="{{ $site['search_tip'] ?? 'Search' }}">
                    <button type="submit" class="site-search__button">Search</button>
                </form>
                <div class="site-search__hotwords">
                    <span>Hot:</span>
                    @foreach(($hotWords ?? []) as $word)
                        <a href="{{ $word['url'] }}">{{ $word['title'] }}</a>
                    @endforeach
                </div>
            </div>
            <nav class="site-nav site-nav--mobile" aria-label="Mobile navigation">
                @foreach(($navigation ?? []) as $item)
                    <a href="{{ $item['url'] }}" class="site-nav__link">{{ $item['title'] }}</a>
                @endforeach
            </nav>
        </div>
        <button type="button" class="site-nav-drawer__mask" data-menu-close aria-label="Close navigation"></button>
    </div>
</header>
