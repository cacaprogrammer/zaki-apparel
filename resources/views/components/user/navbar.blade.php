<header class="site-navbar"
    x-data="{
        mobileOpen: false,
        catOpen: false,
        userOpen: false,
        activeNav: (window.location.hash === '#best-seller') ? 'bestseller'
            : (window.location.hash === '#contact-us') ? 'contact'
            : 'home'
    }">
    <div class="navbar-inner">
        <a href="{{ url('/') }}" class="navbar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Zaki Apparel">
        </a>

        <ul class="navbar-menu">
            <li>
                <a href="{{ url('/') }}"
                   :class="{ 'active': activeNav === 'home' && {{ request()->is('/') ? 'true' : 'false' }} }"
                   @click="activeNav = 'home'">
                    {{ __('layout.nav_home') }}
                </a>
            </li>
            <li class="navbar-dropdown" :class="{ 'open': catOpen }" @mouseenter="catOpen = true" @mouseleave="catOpen = false">
                <a href="{{ route('category') }}" class="nav-cat-link {{ request()->is('category*') ? 'active' : '' }}">
                    {{ __('layout.nav_category') }}
                    <svg viewBox="0 0 24 24" class="chevron"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <div class="navbar-dropdown-panel">
                    <div class="navbar-dropdown-panel-inner">
                        <div class="panel-title">{{ __('layout.shop_by_category') }}</div>

                        <a href="{{ route('category', 'school-uniform') }}" class="cat-item">
                            <span class="cat-name">{{ __('layout.cat_school_uniform') }}</span>
                            <span class="cat-desc">{{ __('layout.cat_school_uniform_desc') }}</span>
                        </a>
                        <a href="{{ route('category', 'classic-shirt') }}" class="cat-item">
                            <span class="cat-name">{{ __('layout.cat_classic_shirt') }}</span>
                            <span class="cat-desc">{{ __('layout.cat_classic_shirt_desc') }}</span>
                        </a>
                        <a href="{{ route('category', 'flannel-shirt') }}" class="cat-item">
                            <span class="cat-name">{{ __('layout.cat_flannel_shirt') }}</span>
                            <span class="cat-desc">{{ __('layout.cat_flannel_shirt_desc') }}</span>
                        </a>
                        <a href="{{ route('category', 'striped-shirt') }}" class="cat-item">
                            <span class="cat-name">{{ __('layout.cat_striped_shirt') }}</span>
                            <span class="cat-desc">{{ __('layout.cat_striped_shirt_desc') }}</span>
                        </a>

                        <a href="{{ route('category') }}" class="view-all">{{ __('layout.view_all_categories') }}</a>
                    </div>
                </div>
            </li>
            <li>
                <a href="{{ url('/') }}#best-seller"
                   :class="{ 'active': activeNav === 'bestseller' }"
                   @click="activeNav = 'bestseller'">
                    {{ __('layout.nav_bestseller') }}
                </a>
            </li>
            <li>
                <a href="{{ url('/') }}#contact-us"
                   :class="{ 'active': activeNav === 'contact' }"
                   @click="activeNav = 'contact'">
                    {{ __('layout.nav_contact') }}
                </a>
            </li>
        </ul>

        <div class="navbar-actions">
            <a href="{{ route('wishlist') }}" class="navbar-icon-btn {{ request()->routeIs('wishlist') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
            </a>
            <a href="{{ route('cart') }}" class="navbar-icon-btn {{ request()->routeIs('cart') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
            </a>

            @auth
                <div class="navbar-user" :class="{ 'open': userOpen }" @click.outside="userOpen = false">
                    <button type="button" class="navbar-icon-btn" @click="userOpen = !userOpen">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.6"/><path d="M4.5 20.5c1.6-4 4.4-6 7.5-6s5.9 2 7.5 6"/></svg>
                    </button>
                    <div class="navbar-user-panel">
                        <div class="navbar-user-header">
                            <div class="navbar-user-avatar">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.6"/><path d="M4.5 20.5c1.6-4 4.4-6 7.5-6s5.9 2 7.5 6"/></svg>
                            </div>
                            <div class="navbar-user-info">
                                <span class="navbar-user-name">{{ auth()->user()->name }}</span>
                                <span class="navbar-user-email">{{ auth()->user()->email }}</span>
                            </div>
                        </div>

                        <div class="navbar-user-divider"></div>

                        <a href="{{ route('profile') }}" class="navbar-user-item">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.6"/><path d="M4.5 20.5c1.6-4 4.4-6 7.5-6s5.9 2 7.5 6"/></svg>
                            {{ __('layout.my_profile') }}
                        </a>

                        <div class="navbar-user-divider"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="navbar-user-item navbar-user-item-danger">
                                <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                {{ __('layout.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="navbar-login-link">
                    {{ __('layout.nav_login') }}
                </a>
            @endauth

            <div class="navbar-lang">
                <a href="{{ route('lang.switch', 'id') }}" class="{{ app()->getLocale() === 'id' ? 'active' : '' }}">ID</a>
                <span>/</span>
                <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            </div>

            <button type="button" class="navbar-burger" @click="mobileOpen = !mobileOpen">
                <svg viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
        </div>
    </div>

    <div class="navbar-mobile-menu" :class="{ 'open': mobileOpen }">
        <a href="{{ url('/') }}"
           :class="{ 'active': activeNav === 'home' && {{ request()->is('/') ? 'true' : 'false' }} }"
           @click="activeNav = 'home'">
            {{ __('layout.nav_home') }}
        </a>
        <a href="{{ route('category') }}" class="{{ request()->is('category*') ? 'active' : '' }}">{{ __('layout.nav_category') }}</a>
        <a href="{{ url('/') }}#best-seller"
           :class="{ 'active': activeNav === 'bestseller' }"
           @click="activeNav = 'bestseller'">
            {{ __('layout.nav_bestseller') }}
        </a>
        <a href="{{ url('/') }}#contact-us"
           :class="{ 'active': activeNav === 'contact' }"
           @click="activeNav = 'contact'">
            {{ __('layout.nav_contact') }}
        </a>

        @auth
            <a href="{{ route('profile') }}">{{ __('layout.my_profile') }}</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="navbar-mobile-logout">{{ __('layout.logout') }}</button>
            </form>
        @else
            <a href="{{ route('login') }}">{{ __('layout.nav_login') }}</a>
        @endauth

        <div class="navbar-lang" style="margin-top:14px;">
            <a href="{{ route('lang.switch', 'id') }}" class="{{ app()->getLocale() === 'id' ? 'active' : '' }}">ID</a>
            <span>/</span>
            <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
        </div>
    </div>
</header>