<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    public string $slug;
    public array $product = [];

    public string $selectedColor = '';
    public string $selectedSize = '';
    public int $quantity = 1;
    public string $activeImage = '';

    public bool $showCartPanel = false;
    public bool $isWishlisted = false;
    public array $lastAddedItem = [];

    public array $openAccordion = ['description' => true, 'size_fit' => false, 'shipping' => false];

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        // Dummy product data
        $this->product = [
            'name' => 'Kids Flannel Shirt',
            'category' => 'Flannel Shirt',
            'price' => 149000,
            'rating' => 4.8,
            'reviews_count' => 123,
            'stock' => 24,
            'colors' => [
                'white-pearl' => ['label' => 'White Pearl', 'hex' => '#F4F1E9'],
                'black' => ['label' => 'Black', 'hex' => '#2C2C2C'],
                'classy-choco' => ['label' => 'Classy Choco', 'hex' => '#6B4A32'],
                'classy-gray' => ['label' => 'Classy Gray', 'hex' => '#8B8577'],
            ],
            'sizes' => ['S', 'M', 'L', 'XL'],
            'images' => [
                '/images/products/flannel-shirt.webp',
                '/images/products/flannel-1.webp',
                '/images/products/flannel-2.webp',
                '/images/products/flannel-3.webp',
            ],
            'description' => 'Soft brushed flannel shirt made for everyday play. Breathable cotton blend keeps your little one comfortable from morning drop-off to evening wind-down.',
            'size_fit_text' => 'Runs true to size. If your child is between sizes, we recommend sizing up for a comfortable, room-to-grow fit.',
            'shipping_text' => "Free shipping on orders above Rp 300,000. Easy 7-day returns if it doesn't fit right.",
        ];

        $this->selectedColor = array_key_first($this->product['colors']);
        $this->selectedSize = $this->product['sizes'][2];
        $this->activeImage = $this->product['images'][0];

        $wishlist = session('wishlist', []);
        $this->isWishlisted = in_array($this->slug, $wishlist);
    }

    public function selectColor(string $key): void
    {
        $this->selectedColor = $key;
    }

    public function selectSize(string $size): void
    {
        $this->selectedSize = $size;
    }

    public function setActiveImage(string $img): void
    {
        $this->activeImage = $img;
    }

    public function incrementQty(): void
    {
        if ($this->quantity < $this->product['stock']) {
            $this->quantity++;
        }
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function toggleAccordion(string $key): void
    {
        $this->openAccordion[$key] = !$this->openAccordion[$key];
    }

    public function toggleWishlist(): void
    {
        $wishlist = session('wishlist', []);

        if (in_array($this->slug, $wishlist)) {
            $wishlist = array_diff($wishlist, [$this->slug]);
            $this->isWishlisted = false;
        } else {
            $wishlist[] = $this->slug;
            $this->isWishlisted = true;
        }

        session(['wishlist' => $wishlist]);

        $this->redirect(route('wishlist'));
    }

    public function addToCart(): void
    {
        $cart = session('cart', []);

        $color = $this->product['colors'][$this->selectedColor]['label'];
        $size = $this->selectedSize;

        // Cek apakah produk dengan slug, warna, dan size yang sama sudah ada di cart
        $existingIndex = null;
        foreach ($cart as $index => $cartItem) {
            if ($cartItem['slug'] === $this->slug && $cartItem['color'] === $color && $cartItem['size'] === $size) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Sudah ada -> gabungkan quantity-nya
            $cart[$existingIndex]['qty'] += $this->quantity;
            $item = $cart[$existingIndex];
        } else {
            // Belum ada -> tambahkan sebagai item baru
            $item = [
                'id' => uniqid(),
                'slug' => $this->slug,
                'name' => $this->product['name'],
                'color' => $color,
                'size' => $size,
                'qty' => $this->quantity,
                'price' => $this->product['price'],
                'image' => $this->activeImage,
            ];

            $cart[] = $item;
        }

        session(['cart' => $cart]);

        $this->lastAddedItem = $item;
        $this->showCartPanel = true;

        $this->dispatch('cart-updated', count: count($cart));
    }

    public function closeCartPanel(): void
    {
        $this->showCartPanel = false;
    }

    #[On('size-selected')]
    public function setSizeFromModal(string $size): void
    {
        $this->selectedSize = $size;
    }

    public function getCartCountProperty(): int
    {
        return count(session('cart', []));
    }

    public function getCartSubtotalProperty(): int
    {
        return array_sum(array_map(fn($i) => $i['price'] * $i['qty'], session('cart', [])));
    }
};
?>

<div>

    {{-- SECTION 1: GALLERY & PRODUCT DETAIL --}}
    <div class="product-layout">

        {{-- GALLERY --}}
        <div>
            <div class="gallery-main">
                <img src="{{ $activeImage }}" alt="{{ $product['name'] }}">
            </div>
            <div class="gallery-thumbs">
                @foreach(array_slice($product['images'], 1) as $img)
                    <div class="gallery-thumb {{ $activeImage === $img ? 'active' : '' }}" wire:click="setActiveImage('{{ $img }}')">
                        <img src="{{ $img }}" alt="">
                    </div>
                @endforeach
            </div>

            {{-- RATING SUMMARY --}}
            <div class="rating-summary-mini">
                <div class="rating-summary-score">
                    <span class="score-num">{{ number_format($product['rating'], 1) }}</span>
                    <div class="score-side">
                        <div class="score-stars">★★★★★</div>
                        <div class="score-sub">Based on {{ $product['reviews_count'] }} reviews</div>
                    </div>
                </div>
                @foreach(['5' => 108, '4' => 17, '3' => 5, '2' => 1, '1' => 1] as $star => $count)
                    <div class="rating-bar-row">
                        <span class="bar-star">{{ $star }}</span>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ round(($count/132)*100, 1) }}%"></div></div>
                        <span class="bar-count">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PRODUCT INFO --}}
        <div class="product-info">
            <div class="head-row">
                <div>
                    <span class="product-eyebrow">{{ $product['category'] }}</span>
                    <h1>{{ $product['name'] }}</h1>
                </div>
            </div>

            <div class="price-block">Rp {{ number_format($product['price'], 0, ',', '.') }}</div>

            <div class="rating-row">
                <span class="stars">★★★★★</span>
                <span>{{ $product['rating'] }} ({{ $product['reviews_count'] }} Reviews)</span>
            </div>

            <div class="select-group">
                <div class="label-row">
                    <span class="label">{{ __('product.select_color') }}</span>
                </div>
                <div class="color-chips">
                    @foreach($product['colors'] as $key => $color)
                        <div class="color-chip {{ $selectedColor === $key ? 'active' : '' }}" wire:click="selectColor('{{ $key }}')">
                            {{ $color['label'] }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="select-group">
                <div class="label-row">
                    <span class="label">{{ __('product.select_size') }}</span>
                    <button class="size-guide-link" wire:click="$dispatch('open-size-modal')">
                        {{ __('product.size_recommendation') }}
                    </button>
                </div>
                <div class="size-chips">
                    @foreach($product['sizes'] as $size)
                        <div class="size-chip {{ $selectedSize === $size ? 'active' : '' }}" wire:click="selectSize('{{ $size }}')">
                            {{ $size }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="qty-row">
                <div class="qty-stepper">
                    <button wire:click="decrementQty">−</button>
                    <span>{{ $quantity }}</span>
                    <button wire:click="incrementQty">+</button>
                </div>
                <span class="stock-note">{{ __('product.in_stock', ['count' => $product['stock']]) }}</span>
            </div>

            <div class="cta-row">
                <button class="btn-add-cart" wire:click="addToCart">
                    {{ __('product.add_to_cart') }}
                </button>
                <button class="btn-wish {{ $isWishlisted ? 'active' : '' }}" wire:click="toggleWishlist">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#1B1B22" stroke-width="1.8">
                        <path d="M20.8 8.6c0-3.1-2.5-5.4-5.4-5.4-1.7 0-3.3.9-4.3 2.3-1-1.4-2.6-2.3-4.3-2.3-2.9 0-5.4 2.3-5.4 5.4 0 6 9.7 11.4 9.7 11.4s9.7-5.4 9.7-11.4z"/>
                    </svg>
                </button>
            </div>

            <button class="btn-buy-now">{{ __('product.buy_now') }}</button>

            <div class="accordion">
                <div class="accordion-item">
                    <div class="accordion-head" wire:click="toggleAccordion('description')">
                        <h4>{{ __('product.product_description') }}</h4>
                        <span class="accordion-icon">{{ $openAccordion['description'] ? '×' : '+' }}</span>
                    </div>
                    <div class="accordion-body" style="max-height: {{ $openAccordion['description'] ? '200px' : '0' }}">
                        <p>{{ $product['description'] }}</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-head" wire:click="toggleAccordion('size_fit')">
                        <h4>{{ __('product.size_fit') }}</h4>
                        <span class="accordion-icon">{{ $openAccordion['size_fit'] ? '×' : '+' }}</span>
                    </div>
                    <div class="accordion-body" style="max-height: {{ $openAccordion['size_fit'] ? '200px' : '0' }}">
                        <p>{{ $product['size_fit_text'] }}</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-head" wire:click="toggleAccordion('shipping')">
                        <h4>{{ __('product.shipping_returns') }}</h4>
                        <span class="accordion-icon">{{ $openAccordion['shipping'] ? '×' : '+' }}</span>
                    </div>
                    <div class="accordion-body" style="max-height: {{ $openAccordion['shipping'] ? '200px' : '0' }}">
                        <p>{{ $product['shipping_text'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- MINI CART PANEL --}}
        @if($showCartPanel)
            <div class="mini-cart-overlay" wire:click="closeCartPanel"></div>
            <div class="mini-cart-panel">
                <div class="mini-cart-head">
                    <h3>{{ __('product.shopping_cart') }} ({{ $this->cartCount }})</h3>
                    <button wire:click="closeCartPanel">&times;</button>
                </div>

                @if(!empty($lastAddedItem))
                    <div class="mini-cart-item">
                        <img src="{{ $lastAddedItem['image'] }}" alt="">
                        <div class="info">
                            <div class="name">{{ $lastAddedItem['name'] }}</div>
                            <div class="meta">{{ $lastAddedItem['color'] }}, {{ $lastAddedItem['size'] }} · Qty {{ $lastAddedItem['qty'] }}</div>
                            <div class="price">Rp {{ number_format($lastAddedItem['price'] * $lastAddedItem['qty'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                @endif

                <div class="mini-cart-foot">
                    <div class="subtotal-row">
                        <span>{{ __('product.subtotal') }}</span>
                        <span>Rp {{ number_format($this->cartSubtotal, 0, ',', '.') }}</span>
                    </div>
                    <a href="/cart">{{ __('product.view_cart') }}</a>
                </div>
            </div>
        @endif

        {{-- SIZE RECOMMENDATION MODAL --}}
        <livewire:product.size-recommendation />
    </div>

    {{-- SECTION 2: FULL-WIDTH REVIEWS & TABS (DITARUH DI LUAR .product-layout) --}}
    <livewire:product.reviews />
</div>