<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $avatarInitial = '';

    public string $activeTab = 'all';
    public array $orders = [];

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? 'Queenza Akleema';
        $this->email = $user->email ?? 'quincaa@email.com';
        $this->avatarInitial = strtoupper(substr(trim($this->name), 0, 1)) ?: 'U';

        // Dummy data — nanti diganti dengan data order asli dari database
        $this->orders = [
            [
                'id' => 'ZA-84719', 'placed_on' => '1 Sep 2026', 'status' => 'shipped',
                'items_count' => 2, 'total' => 603840, 'refunded' => false,
                'images' => ['/images/products/flannel-shirt.jpg', '/images/products/navy-classic.jpg'],
            ],
            [
                'id' => 'ZA-84702', 'placed_on' => '29 Aug 2026', 'status' => 'processing',
                'items_count' => 1, 'total' => 156840, 'refunded' => false,
                'images' => ['/images/products/flannel-1.jpg'],
            ],
            [
                'id' => 'ZA-84588', 'placed_on' => '14 Aug 2026', 'status' => 'completed',
                'items_count' => 5, 'total' => 892000, 'refunded' => false,
                'images' => ['/images/products/flannel-shirt.jpg', '/images/products/flannel-1.jpg', '/images/products/navy-classic.jpg'],
                'more_count' => 2,
            ],
            [
                'id' => 'ZA-84391', 'placed_on' => '2 Aug 2026', 'status' => 'completed',
                'items_count' => 1, 'total' => 149000, 'refunded' => false,
                'images' => ['/images/products/flannel-2.jpg'],
            ],
            [
                'id' => 'ZA-84150', 'placed_on' => '21 Jul 2026', 'status' => 'cancelled',
                'items_count' => 1, 'total' => 318000, 'refunded' => true,
                'images' => ['/images/products/navy-classic.jpg'],
            ],
        ];
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getTabCountsProperty(): array
    {
        $counts = ['all' => count($this->orders)];
        foreach (['processing', 'shipped', 'completed', 'cancelled'] as $status) {
            $counts[$status] = count(array_filter($this->orders, fn($o) => $o['status'] === $status));
        }
        return $counts;
    }

    public function getFilteredOrdersProperty(): array
    {
        if ($this->activeTab === 'all') {
            return $this->orders;
        }
        return array_values(array_filter($this->orders, fn($o) => $o['status'] === $this->activeTab));
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
                <a href="{{ route('my-orders') }}" class="active">
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
                <a href="#">
                    <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                    {{ __('profile.chat') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout">
                        <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                        {{ __('profile.logout') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- MAIN --}}
        <div class="my-orders-main">
            <div class="my-orders-head">
                <h2>{{ __('my-orders.page_title') }}</h2>
                <div class="sub">{{ __('my-orders.subtitle') }}</div>
            </div>

            <div class="order-tabs">
                <button type="button" class="order-tab {{ $activeTab === 'all' ? 'active' : '' }}" wire:click="setTab('all')">
                    {{ __('my-orders.tab_all') }} ({{ $this->tabCounts['all'] }})
                </button>
                <button type="button" class="order-tab {{ $activeTab === 'processing' ? 'active' : '' }}" wire:click="setTab('processing')">
                    {{ __('my-orders.tab_processing') }} ({{ $this->tabCounts['processing'] }})
                </button>
                <button type="button" class="order-tab {{ $activeTab === 'shipped' ? 'active' : '' }}" wire:click="setTab('shipped')">
                    {{ __('my-orders.tab_shipped') }} ({{ $this->tabCounts['shipped'] }})
                </button>
                <button type="button" class="order-tab {{ $activeTab === 'completed' ? 'active' : '' }}" wire:click="setTab('completed')">
                    {{ __('my-orders.tab_completed') }} ({{ $this->tabCounts['completed'] }})
                </button>
                <button type="button" class="order-tab {{ $activeTab === 'cancelled' ? 'active' : '' }}" wire:click="setTab('cancelled')">
                    {{ __('my-orders.tab_cancelled') }} ({{ $this->tabCounts['cancelled'] }})
                </button>
            </div>

            <div class="order-list">
                @forelse($this->filteredOrders as $order)
                    <div class="order-box" wire:key="order-{{ $order['id'] }}">
                        <div class="order-box-header">
                            <div class="order-box-header-left">
                                <div class="order-id">
                                    {{ __('my-orders.order') }}
                                    <span class="val">#{{ $order['id'] }}</span>
                                </div>
                                <div class="placed-on">
                                    {{ __('my-orders.placed_on') }}
                                    <span class="val">{{ $order['placed_on'] }}</span>
                                </div>
                            </div>
                            <span class="status-badge {{ $order['status'] }}">{{ __('my-orders.status_' . $order['status']) }}</span>
                        </div>

                        <div class="order-box-body">
                            <div class="order-thumbs">
                                @foreach($order['images'] as $img)
                                    <div class="order-thumb">
                                        <img src="{{ $img }}" alt="">
                                    </div>
                                @endforeach
                                @if(!empty($order['more_count']))
                                    <div class="order-thumb more-count">+{{ $order['more_count'] }}</div>
                                @endif
                            </div>

                            <div class="order-box-footer">
                                <div class="summary">
                                    {{ $order['items_count'] }} {{ $order['items_count'] > 1 ? __('my-orders.items') : __('my-orders.item') }}
                                    · {{ $order['refunded'] ? __('my-orders.refunded') : __('my-orders.total_paid') }}
                                    <span class="amount">Rp {{ number_format($order['total'], 0, ',', '.') }}</span>
                                </div>
                                <div class="order-actions">
                                    @if($order['status'] === 'processing')
                                        <button type="button" class="btn-outline-danger">{{ __('my-orders.cancel_order') }}</button>
                                    @elseif(in_array($order['status'], ['shipped', 'completed']))
                                        <button type="button" class="btn-outline">{{ __('my-orders.buy_again') }}</button>
                                    @endif
                                    <a href="{{ route('order.detail', $order['id']) }}" class="btn-solid">{{ __('my-orders.view_order') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="orders-empty">{{ __('my-orders.no_orders') }}</div>
                @endforelse
            </div>
        </div>

    </div>
</div>