<?php

use Livewire\Component;
use App\Concerns\TogglesWishlist;

new class extends Component {
    use TogglesWishlist;

    public array $wishlistItems = [];
    public array $suggestedItems = [];

    public function mount(): void
    {
        // Dummy data — nanti diganti query database sungguhan
        $this->wishlistItems = [
            ['id' => 1, 'slug' => 'kids-school-shirt', 'name' => 'Kids Short-Sleeve School Shirt', 'category' => 'School Uniform', 'price' => 125000, 'image' => '/images/products/school-shirt.jpg', 'badge' => 'New'],
            ['id' => 2, 'slug' => 'navy-classic-shirt', 'name' => 'Navy Classic Shirt', 'category' => 'Classic Shirt', 'price' => 159000, 'image' => '/images/products/navy-classic.jpg', 'badge' => null],
            ['id' => 3, 'slug' => 'blossom-pink-striped-shirt', 'name' => 'Blossom Pink Striped Shirt', 'category' => 'Striped Shirt', 'price' => 129000, 'image' => '/images/products/striped-shirt.jpg', 'badge' => 'Sale'],
            ['id' => 4, 'slug' => 'clay-flannel-shirt', 'name' => 'Clay Flannel Shirt', 'category' => 'Flannel Shirt', 'price' => 149000, 'image' => '/images/products/flannel-2.jpg', 'badge' => null],
            ['id' => 5, 'slug' => 'sage-green-classic-shirt', 'name' => 'Sage Green Classic Shirt', 'category' => 'Classic Shirt', 'price' => 135000, 'image' => '/images/products/flannel-1.jpg', 'badge' => null],
            ['id' => 6, 'slug' => 'golden-sand-flannel-shirt', 'name' => 'Golden Sand Flannel Shirt', 'category' => 'Flannel Shirt', 'price' => 145000, 'image' => '/images/products/flannel-3.jpg', 'badge' => 'New'],
        ];

        $this->suggestedItems = [
            ['id' => 7, 'slug' => 'long-sleeve-school-shirt', 'name' => 'Long-Sleeve School Shirt', 'category' => 'School Uniform', 'price' => 135000, 'image' => '/images/products/school-shirt.jpg', 'badge' => null],
            ['id' => 8, 'slug' => 'classic-white-striped-shirt', 'name' => 'Classic White Striped Shirt', 'category' => 'Striped Shirt', 'price' => 119000, 'image' => '/images/products/striped-shirt.jpg', 'badge' => null],
            ['id' => 9, 'slug' => 'sunny-yellow-tee', 'name' => 'Sunny Yellow Tee', 'category' => 'Classic Shirt', 'price' => 99000, 'image' => '/images/products/flannel-shirt.jpg', 'badge' => 'New'],
            ['id' => 10, 'slug' => 'classic-khaki-shorts', 'name' => 'Classic Khaki Shorts', 'category' => 'Classic Shirt', 'price' => 115000, 'image' => '/images/products/flannel-1.jpg', 'badge' => null],
        ];
    }

    public function removeItem(int $id): void
    {
        $this->wishlistItems = array_values(
            array_filter($this->wishlistItems, fn($item) => $item['id'] !== $id)
        );
    }

    public function addAllToCart(): void
    {
        $cart = session('cart', []);

        foreach ($this->wishlistItems as $item) {
            $cart[] = [
                'id' => uniqid(),
                'slug' => $item['slug'],
                'name' => $item['name'],
                'color' => '-',
                'size' => '-',
                'qty' => 1,
                'price' => $item['price'],
                'image' => $item['image'],
            ];
        }

        session(['cart' => $cart]);
        $this->dispatch('cart-updated', count: count($cart));
    }

    public function getEstimatedTotalProperty(): int
    {
        return array_sum(array_column($this->wishlistItems, 'price'));
    }
};
?>

<div class="wrap">


    <div class="page-title-block">
        <div>
            <h1>{{ __('wishlist.title') }}</h1>
            <div class="count">{{ count($wishlistItems) }} {{ __('wishlist.items_saved') }}</div>
        </div>
    </div>

    <div class="action-bar">
        <div class="left">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20.8 8.6c0-3.1-2.5-5.4-5.4-5.4-1.7 0-3.3.9-4.3 2.3-1-1.4-2.6-2.3-4.3-2.3-2.9 0-5.4 2.3-5.4 5.4 0 6 9.7 11.4 9.7 11.4s9.7-5.4 9.7-11.4z"/></svg>
            <span>{{ __('wishlist.estimated_total') }} <b>Rp {{ number_format($this->estimatedTotal, 0, ',', '.') }}</b></span>
        </div>
        <div class="right">
            <button class="btn-outline">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 10.5l6.8-3.8"/><path d="M8.6 13.5l6.8 3.8"/></svg>
                {{ __('wishlist.share') }}
            </button>
            <button class="btn-solid" wire:click="addAllToCart">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6L4.5 3H2"/></svg>
                {{ __('wishlist.add_all') }}
            </button>
        </div>
    </div>

    <div class="wishlist-section">

        @if(count($wishlistItems) > 0)
            <div class="wishlist-grid">
                @foreach($wishlistItems as $item)
                    <div class="product-card" wire:key="wish-{{ $item['id'] }}">
                        <div class="img-wrap">
                            @if($item['badge'])
                                <span class="product-badge">{{ $item['badge'] }}</span>
                            @endif
                            <button class="product-fav active" wire:click="removeItem({{ $item['id'] }})" title="{{ __('wishlist.remove') }}">
                                <svg viewBox="0 0 24 24" fill="currentColor" stroke="none">
                                    <path d="M12 21s-6.7-4.35-9.3-8.1C.8 9.7 2 6 5.4 5 7.6 4.3 9.8 5.2 12 7.4 14.2 5.2 16.4 4.3 18.6 5c3.4 1 4.6 4.7 2.7 7.9C18.7 16.65 12 21 12 21z"/>
                                </svg>
                            </button>
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                        </div>
                        <div class="product-info">
                            <span class="cat-label">{{ $item['category'] }}</span>
                            <h4>{{ $item['name'] }}</h4>
                            <div class="price">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                            <a href="/product/{{ $item['slug'] }}" class="product-view-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
                                {{ __('wishlist.view_product') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="empty-wishlist {{ count($wishlistItems) === 0 ? 'show' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M20.8 8.6c0-3.1-2.5-5.4-5.4-5.4-1.7 0-3.3.9-4.3 2.3-1-1.4-2.6-2.3-4.3-2.3-2.9 0-5.4 2.3-5.4 5.4 0 6 9.7 11.4 9.7 11.4s9.7-5.4 9.7-11.4z"/></svg>
            <h3>{{ __('wishlist.empty_title') }}</h3>
            <p>{{ __('wishlist.empty_desc') }}</p>
            <a href="/category" class="btn-solid">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
                {{ __('wishlist.start_browsing') }}
            </a>
        </div>
    </div>

    <div class="also-like">
        <div class="also-like-head">
            <h2>{{ __('wishlist.you_might_also_like') }}</h2>
            <a href="/category">
                {{ __('wishlist.view_all') }}
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
            </a>
        </div>
        <div class="product-grid">
            @foreach($suggestedItems as $item)
                <div class="product-card" wire:key="suggest-{{ $item['id'] }}">
                    <div class="img-wrap">
                        @if($item['badge'])
                            <span class="product-badge">{{ $item['badge'] }}</span>
                        @endif
                        <button
                            type="button"
                            wire:click="toggleWishlist('{{ $item['slug'] }}')"
                            class="product-fav {{ $this->isWishlisted($item['slug']) ? 'active' : '' }}"
                            title="{{ __('wishlist.add_to_wishlist') }}"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.8 8.6c0-3.1-2.5-5.4-5.4-5.4-1.7 0-3.3.9-4.3 2.3-1-1.4-2.6-2.3-4.3-2.3-2.9 0-5.4 2.3-5.4 5.4 0 6 9.7 11.4 9.7 11.4s9.7-5.4 9.7-11.4z"/>
                            </svg>
                        </button>
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                    </div>
                    <div class="product-info">
                        <span class="cat-label">{{ $item['category'] }}</span>
                        <h4>{{ $item['name'] }}</h4>
                        <div class="price">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                        <a href="/product/{{ $item['slug'] }}" class="product-view-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
                            {{ __('wishlist.view_product') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>