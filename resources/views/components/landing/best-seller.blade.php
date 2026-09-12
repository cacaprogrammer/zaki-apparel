<?php
$products = [
    [
        'name' => __('landing.product_checkered'),
        'category' => __('landing.cat_striped'),
        'price' => 66000,
        'image' => 'checkered-shirt.jpeg',
        'badge' => true,
        'slug' => 'checkered-shirt',
    ],
    [
        'name' => __('landing.product_striped'),
        'category' => __('landing.cat_striped'),
        'price' => 64000,
        'image' => 'striped-shirt.jpeg',
        'badge' => false,
        'slug' => 'striped-shirt',
    ],
    [
        'name' => __('landing.product_flannel'),
        'category' => __('landing.cat_flannel'),
        'price' => 72000,
        'image' => 'flannel-shirt.webp',
        'badge' => true,
        'slug' => 'flannel-shirt',
    ],
    [
        'name' => __('landing.product_casual'),
        'category' => __('landing.cat_classic'),
        'price' => 75000,
        'image' => 'casual-shirt.webp',
        'badge' => false,
        'slug' => 'casual-shirt',
    ],
];
?>

<section class="bestseller-section" id="best-seller">
    <div class="bestseller-inner">
        <div class="bestseller-eyebrow">{{ __('landing.bestseller_eyebrow') }}</div>
        <h2 class="bestseller-heading">{{ __('landing.bestseller_heading') }}</h2>

        <div class="product-grid">
            @foreach ($products as $product)
                <div class="product-card">
                    <div class="img-wrap">
                        <img src="{{ asset('images/' . $product['image']) }}" alt="{{ $product['name'] }}">
                        @if ($product['badge'])
                            <span class="product-badge">{{ __('landing.badge_new') }}</span>
                        @endif
                        <a href="{{ route('wishlist') }}" class="product-fav">
                            <svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
                        </a>
                    </div>
                    <div class="product-info">
                        <div class="cat-label">{{ $product['category'] }}</div>
                        <h4>{{ $product['name'] }}</h4>
                        <div class="price">Rp {{ number_format($product['price'], 0, ',', '.') }}</div>
                        <a href="{{ route('product.detail', ['slug' => $product['slug']]) }}" class="product-view-btn">
                            <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
                            {{ __('landing.view_product') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <a href="#" class="view-all-btn">{{ __('landing.view_all_product') }}</a>
    </div>
</section>