<footer class="site-footer">
    <div class="site-footer__links">
        <div class="site-container">
            <div class="site-section-heading is-compact">
                <div>
                    <p class="site-section-heading__eyebrow">Friendly Links</p>
                    <h2 class="site-section-heading__title">合作伙伴与友情链接</h2>
                </div>
            </div>
            <div class="friend-links">
                @foreach(($links ?? []) as $link)
                    <a href="{{ $link['url'] }}" class="friend-links__item">
                        <img src="{{ $link['logo'] }}" alt="{{ $link['title'] }}">
                        <span>{{ $link['title'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="site-footer__main">
        <div class="site-container site-footer__grid">
            <div class="site-footer__brand">
                <div class="site-brand site-brand--footer">
                    <span class="site-brand__logo-wrap">
                        <img src="{{ $site['logo'] ?? '/static/common/images/logo-1.png' }}" alt="{{ $site['brand_name'] ?? '' }}" class="site-brand__logo">
                    </span>
                    <span class="site-brand__meta">
                        <strong>{{ $site['brand_name'] ?? '' }}</strong>
                        <small>{{ $site['site_name'] ?? '' }}</small>
                    </span>
                </div>
                <p class="site-footer__summary">{{ $site['footer_intro'] ?? ($site['tagline'] ?? '') }}</p>
                <p class="site-footer__summary is-tip">{{ $site['footer_contact_tip'] ?? '' }}</p>
            </div>
            <div>
                <h3 class="site-footer__title">Quick Navigation</h3>
                <ul class="site-footer__list">
                    @foreach(($navigation ?? []) as $item)
                        <li><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h3 class="site-footer__title">Hot Search</h3>
                <ul class="site-footer__list">
                    @foreach(($hotWords ?? []) as $word)
                        <li><a href="{{ $word['url'] }}">{{ $word['title'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h3 class="site-footer__title">Contact</h3>
                <ul class="site-footer__list site-footer__contact">
                    <li><span>Phone</span><strong>{{ $site['phone'] ?? '' }}</strong></li>
                    <li><span>Email</span><a href="mailto:{{ $site['email'] ?? '' }}">{{ $site['email'] ?? '' }}</a></li>
                    <li><span>Address</span><em>{{ $site['address'] ?? '' }}</em></li>
                    <li><span>Agent</span><strong>{{ $site['agent_phone'] ?? ($site['phone'] ?? '') }}</strong></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="site-container site-footer__bottom-inner">
            <p>{{ $site['copyright'] ?? '' }}</p>
            <p>{{ $site['beian'] ?? '' }}</p>
        </div>
    </div>
</footer>
