<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="{{ url('/') }}" class="navbar-logo">
    <img src="{{ asset('images/logo.png') }}" alt="Zaki Apparel">
</a>
            <p class="footer-desc">{{ __('layout.footer_desc') }}</p>

            <div class="footer-social-label">{{ __('layout.follow_us') }}</div>
            <div class="footer-social">
                <a href="#"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="3.5"/><circle cx="17.5" cy="6.5" r="0.6" fill="#C79A46"/></svg></a>
                <a href="#"><svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
                <a href="#"><svg viewBox="0 0 24 24"><path d="M22 4.01c-1 .49-1.98.689-3 .99-1.121-1.265-2.783-1.335-4.38-.737S11.977 6.323 12 8v1c-3.245.083-6.135-1.395-8-4 0 0-4.182 7.433 4 11-1.872 1.247-3.739 2.088-6 2 3.308 1.803 6.913 2.423 10.034 1.517 3.58-1.04 6.522-3.723 7.651-7.742a13.84 13.84 0 0 0 .497-3.753C20.18 7.773 21.692 5.25 22 4.009z"/></svg></a>
            </div>
        </div>

        <div class="footer-col">
            <h4>{{ __('layout.footer_about_title') }}</h4>
            <a href="#">{{ __('layout.footer_contact') }}</a>
            <a href="#">{{ __('layout.footer_about_us') }}</a>
            <a href="#">{{ __('layout.footer_careers') }}</a>
        </div>

        <div class="footer-col">
            <h4>{{ __('layout.footer_shop_title') }}</h4>
            <a href="#">{{ __('layout.footer_men') }}</a>
            <a href="#">{{ __('layout.footer_women') }}</a>
            <a href="#">{{ __('layout.footer_kids') }}</a>
        </div>

        <div class="footer-col">
            <h4>{{ __('layout.footer_info_title') }}</h4>
            <a href="#">{{ __('layout.footer_help') }}</a>
            <a href="#">{{ __('layout.footer_shipping_policy') }}</a>
            <a href="#">{{ __('layout.footer_terms') }}</a>
        </div>

        <div class="footer-newsletter">
            <h4>{{ __('layout.footer_newsletter_title') }}</h4>
            <p>{{ __('layout.footer_newsletter_desc') }}</p>
            <form class="footer-newsletter-form" onsubmit="return false;">
                <input type="email" placeholder="{{ __('layout.footer_newsletter_placeholder') }}">
                <button type="submit">{{ __('layout.footer_subscribe') }}</button>
            </form>
        </div>
    </div>

    <div class="footer-bottom">
        <span>{{ __('layout.footer_copyright', ['year' => date('Y')]) }}</span>
        <div>
            <a href="#">{{ __('layout.footer_privacy') }}</a>
            <a href="#">{{ __('layout.footer_terms') }}</a>
        </div>
    </div>
</footer>