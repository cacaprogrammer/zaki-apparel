<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public bool $showLogoutModal = false;

    public string $name = '';
    public string $email = '';
    public string $avatarInitial = '';

    public array $sections = [];

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? 'Queenza';
        $this->email = $user->email ?? 'quincaa@email.com';
        $this->avatarInitial = strtoupper(substr(trim($this->name), 0, 1)) ?: 'U';

        // Dummy data — nanti diganti dengan data notifikasi asli dari database
        $this->sections = [
            [
                'label' => __('notifications.today'),
                'items' => [
                    [
                        'id' => 1, 'type' => 'order', 'unread' => true,
                        'title' => __('notifications.n1_title'),
                        'desc' => __('notifications.n1_desc'),
                        'time' => __('notifications.n1_time'),
                        'cta' => __('notifications.view_order'), 'cta_route' => 'order.detail', 'cta_param' => 'ZA-84722',
                    ],
                    [
                        'id' => 2, 'type' => 'promo', 'unread' => true,
                        'title' => __('notifications.n2_title'),
                        'desc' => __('notifications.n2_desc'),
                        'time' => __('notifications.n2_time'),
                        'cta' => __('notifications.shop_now'), 'cta_route' => 'category', 'cta_param' => null,
                    ],
                ],
            ],
            [
                'label' => __('notifications.yesterday'),
                'items' => [
                    [
                        'id' => 3, 'type' => 'order', 'unread' => true,
                        'title' => __('notifications.n3_title'),
                        'desc' => __('notifications.n3_desc'),
                        'time' => __('notifications.n3_time'),
                        'cta' => null, 'cta_route' => null, 'cta_param' => null,
                    ],
                    [
                        'id' => 4, 'type' => 'stock', 'unread' => true,
                        'title' => __('notifications.n4_title'),
                        'desc' => __('notifications.n4_desc'),
                        'time' => __('notifications.n4_time'),
                        'cta' => __('notifications.view_product'), 'cta_route' => 'wishlist', 'cta_param' => null,
                    ],
                    [
                        'id' => 5, 'type' => 'points', 'unread' => true,
                        'title' => __('notifications.n5_title'),
                        'desc' => __('notifications.n5_desc'),
                        'time' => __('notifications.n5_time'),
                        'cta' => null, 'cta_route' => null, 'cta_param' => null,
                    ],
                ],
            ],
            [
                'label' => __('notifications.this_week'),
                'items' => [
                    [
                        'id' => 6, 'type' => 'review', 'unread' => false,
                        'title' => __('notifications.n6_title'),
                        'desc' => __('notifications.n6_desc'),
                        'time' => __('notifications.n6_time'),
                        'cta' => __('notifications.write_review'), 'cta_route' => null, 'cta_param' => null,
                    ],
                    [
                        'id' => 7, 'type' => 'promo', 'unread' => false,
                        'title' => __('notifications.n7_title'),
                        'desc' => __('notifications.n7_desc'),
                        'time' => __('notifications.n7_time'),
                        'cta' => null, 'cta_route' => null, 'cta_param' => null,
                    ],
                ],
            ],
        ];
    }

    public function getUnreadCountProperty(): int
    {
        $count = 0;
        foreach ($this->sections as $section) {
            foreach ($section['items'] as $item) {
                if ($item['unread']) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function markAllRead(): void
    {
        foreach ($this->sections as $si => $section) {
            foreach ($section['items'] as $ii => $item) {
                $this->sections[$si]['items'][$ii]['unread'] = false;
            }
        }
    }

    public function markOneRead(int $id): void
    {
        foreach ($this->sections as $si => $section) {
            foreach ($section['items'] as $ii => $item) {
                if ($item['id'] === $id) {
                    $this->sections[$si]['items'][$ii]['unread'] = false;
                }
            }
        }
    }
};
?>

<div class="profile-wrap">
    <div class="profile-layout">

        {{-- SIDEBAR (sama seperti Profile) --}}
        <div class="profile-sidebar">
            <div class="profile-sidebar-head">
                <div class="profile-avatar profile-avatar-initial">{{ $avatarInitial }}</div>
                <div>
                    <div class="name">{{ $name }}</div>
                    <div class="email">{{ $email }}</div>
                </div>
            </div>

            <div class="profile-nav">
                <a href="{{ route('profile') }}">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 000 18z" fill="currentColor" stroke="none"/></svg>
                    {{ __('profile.personal_information') }}
                </a>
                <a href="{{ route('my-orders') }}">
                    <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 4v2M16 4v2"/></svg>
                    {{ __('profile.my_orders') }}
                </a>
                <a href="{{ route('address') }}">
                    <svg viewBox="0 0 24 24"><path d="M12 21s-7-6.5-7-11a7 7 0 0114 0c0 4.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    {{ __('profile.address') }}
                </a>
                <a href="{{ route('settings') }}">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.34 1.87l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.7 1.7 0 00-1.87-.34 1.7 1.7 0 00-1 1.55V21a2 2 0 01-4 0v-.09a1.7 1.7 0 00-1-1.55 1.7 1.7 0 00-1.87.34l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.7 1.7 0 00.34-1.87 1.7 1.7 0 00-1.55-1H3a2 2 0 010-4h.09a1.7 1.7 0 001.55-1 1.7 1.7 0 00-.34-1.87l-.06-.06a2 2 0 112.83-2.83l.06.06a1.7 1.7 0 001.87.34H9a1.7 1.7 0 001-1.55V3a2 2 0 014 0v.09a1.7 1.7 0 001 1.55 1.7 1.7 0 001.87-.34l.06-.06a2 2 0 112.83 2.83l-.06.06a1.7 1.7 0 00-.34 1.87V9a1.7 1.7 0 001.55 1H21a2 2 0 010 4h-.09a1.7 1.7 0 00-1.55 1z"/></svg>
                    {{ __('profile.settings') }}
                </a>
                <a href="{{ route('chat') }}">
                    <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                    {{ __('profile.chat') }}
                </a>
                <a href="{{ route('notifications') }}" class="active">
                    <svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 5-2 6-2 6h16s-2-1-2-6"/><path d="M10 19a2 2 0 0 0 4 0"/></svg>
                    {{ __('profile.notifications') }}
                    @if($this->unreadCount > 0)
                        <span class="nav-badge-count">{{ $this->unreadCount }}</span>
                    @endif
                </a>

                <button type="button" class="logout" wire:click="$set('showLogoutModal', true)">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                    {{ __('profile.logout') }}
                </button>
            </div>
        </div>

        @if($showLogoutModal)
            <div class="logout-modal-overlay">
                <div class="logout-modal-box">
                    <div class="logout-modal-icon">
                        <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                    </div>
                    <h3>{{ __('profile.logout_confirm_title') }}</h3>
                    <p>{{ __('profile.logout_confirm_desc') }}</p>
                    <div class="logout-modal-actions">
                        <button type="button" class="logout-cancel-btn" wire:click="$set('showLogoutModal', false)">{{ __('profile.cancel') }}</button>
                        <form method="POST" action="{{ route('logout') }}" style="flex:1;">
                            @csrf
                            <button type="submit" class="logout-confirm-btn" style="width:100%;">{{ __('profile.confirm_logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- MAIN --}}
        <div>
            <div class="notif-main-head">
                <div>
                    <h2>{{ __('notifications.page_title') }}</h2>
                    <div class="sub">{{ __('notifications.unread_count', ['count' => $this->unreadCount]) }}</div>
                </div>
                @if($this->unreadCount > 0)
                    <button type="button" class="mark-all-read-btn" wire:click="markAllRead">{{ __('notifications.mark_all_read') }}</button>
                @endif
            </div>

            <div class="notif-scroll-area">
                @forelse($sections as $section)
                    <div class="notif-section">
                        <div class="notif-date-label">{{ $section['label'] }}</div>

                        @foreach($section['items'] as $item)
                            <div class="notif-item {{ $item['unread'] ? 'unread' : '' }}" wire:click="markOneRead({{ $item['id'] }})">
                                @if($item['unread'])
                                    <div class="notif-unread-dot"></div>
                                @else
                                    <div class="notif-spacer-dot"></div>
                                @endif

                                <div class="notif-icon-box {{ $item['type'] }}">
                                    @if($item['type'] === 'order')
                                        <svg viewBox="0 0 24 24"><rect x="1" y="7" width="15" height="10" rx="1.5"/><path d="M16 10h4l3 3v4h-7z"/><circle cx="6" cy="19" r="1.6"/><circle cx="18.5" cy="19" r="1.6"/></svg>
                                    @elseif($item['type'] === 'promo')
                                        <svg viewBox="0 0 24 24"><path d="M12 2l2 7h7l-5.5 4.5L17 20l-5-3.5L7 20l1.5-6.5L3 9h7z"/></svg>
                                    @elseif($item['type'] === 'stock')
                                        <svg viewBox="0 0 24 24"><path d="M20.8 8.6c0 4-8.8 10-8.8 10s-8.8-6-8.8-10a5 5 0 0 1 8.8-3.2A5 5 0 0 1 20.8 8.6z"/></svg>
                                    @elseif($item['type'] === 'points')
                                        <svg viewBox="0 0 24 24"><path d="M12 2l2.4 7.2H22l-6 4.6 2.3 7.2L12 16.6l-6.3 4.4 2.3-7.2-6-4.6h7.6z"/></svg>
                                    @else
                                        <svg viewBox="0 0 24 24"><path d="M12 2l2.4 7.2H22l-6 4.6 2.3 7.2L12 16.6l-6.3 4.4 2.3-7.2-6-4.6h7.6z"/></svg>
                                    @endif
                                </div>

                                <div class="notif-body">
                                    <div class="top-line">
                                        <h4>{{ $item['title'] }}</h4>
                                        <span class="time">{{ $item['time'] }}</span>
                                    </div>
                                    <p>{{ $item['desc'] }}</p>
                                    @if($item['cta'])
                                        @if($item['cta_route'])
                                            <a href="{{ $item['cta_param'] ? route($item['cta_route'], $item['cta_param']) : route($item['cta_route']) }}" class="cta">{{ $item['cta'] }} <span class="cta-arrow">›</span></a>
                                        @else
                                            <span class="cta">{{ $item['cta'] }} <span class="cta-arrow">›</span></span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="notif-empty">{{ __('notifications.no_notifications') }}</div>
                @endforelse
            </div>
        </div>

    </div>
</div>