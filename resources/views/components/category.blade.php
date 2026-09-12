<?php

use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public ?string $slug = null;
    public string $search = '';
    public array $selectedCategories = [];
    public array $selectedSizes = [];
    public ?float $priceMin = null;
    public ?float $priceMax = null;

    public array $allProducts = [];
    public array $categoryLabels = [
        'school-uniform' => 'School Uniform',
        'flannel-shirt'  => 'Flannel Shirt',
        'classic-shirt'  => 'Classic Shirt',
        'striped-shirt'  => 'Striped Shirt',
    ];

    public function mount(?string $slug = null): void
    {
        $this->slug = $slug;
        $this->loadDummyProducts();

        if ($slug && array_key_exists($slug, $this->categoryLabels)) {
            $this->selectedCategories = [$slug];
        }
    }

    protected function loadDummyProducts(): void
    {
        $this->allProducts = [
            ['id'=>1,'slug'=>'kids-school-shirt','name'=>'Kids School Shirt','category'=>'school-uniform','price'=>125000,'size'=>['S','M','L'],'image'=>'/images/products/school-shirt.jpg','badge'=>'New'],
            ['id'=>2,'slug'=>'long-sleeve-school-shirt','name'=>'Long-Sleeve School Shirt','category'=>'school-uniform','price'=>135000,'size'=>['M','L','XL'],'image'=>'/images/products/school-shirt.jpg','badge'=>null],
            ['id'=>3,'slug'=>'school-polo-shirt','name'=>'School Polo Shirt','category'=>'school-uniform','price'=>119000,'size'=>['S','M'],'image'=>'/images/products/school-shirt.jpg','badge'=>null],
            ['id'=>4,'slug'=>'school-shirt-navy','name'=>'Navy School Shirt','category'=>'school-uniform','price'=>129000,'size'=>['L','XL'],'image'=>'/images/products/school-shirt.jpg','badge'=>null],
            ['id'=>5,'slug'=>'school-shirt-white','name'=>'White School Shirt','category'=>'school-uniform','price'=>115000,'size'=>['S','M','L'],'image'=>'/images/products/school-shirt.jpg','badge'=>'New'],
            ['id'=>6,'slug'=>'school-vest','name'=>'School Vest','category'=>'school-uniform','price'=>149000,'size'=>['M','L'],'image'=>'/images/products/school-shirt.jpg','badge'=>null],

            ['id'=>7,'slug'=>'kids-flannel-shirt','name'=>'Kids Flannel Shirt','category'=>'flannel-shirt','price'=>149000,'size'=>['S','M','L','XL'],'image'=>'/images/products/flannel-shirt.jpg','badge'=>'New'],
            ['id'=>8,'slug'=>'clay-flannel-shirt','name'=>'Clay Flannel Shirt','category'=>'flannel-shirt','price'=>149000,'size'=>['M','L'],'image'=>'/images/products/flannel-2.jpg','badge'=>null],
            ['id'=>9,'slug'=>'golden-sand-flannel-shirt','name'=>'Golden Sand Flannel Shirt','category'=>'flannel-shirt','price'=>145000,'size'=>['S','M'],'image'=>'/images/products/flannel-3.jpg','badge'=>null],
            ['id'=>10,'slug'=>'gray-flannel-shirt','name'=>'Gray Flannel Shirt','category'=>'flannel-shirt','price'=>139000,'size'=>['L','XL'],'image'=>'/images/products/flannel-1.jpg','badge'=>null],

            ['id'=>11,'slug'=>'navy-classic-shirt','name'=>'Navy Classic Shirt','category'=>'classic-shirt','price'=>159000,'size'=>['S','M','L'],'image'=>'/images/products/navy-classic.jpg','badge'=>null],
            ['id'=>12,'slug'=>'sage-green-classic-shirt','name'=>'Sage Green Classic Shirt','category'=>'classic-shirt','price'=>135000,'size'=>['M','L','XL'],'image'=>'/images/products/flannel-1.jpg','badge'=>null],
            ['id'=>13,'slug'=>'sunny-yellow-tee','name'=>'Sunny Yellow Tee','category'=>'classic-shirt','price'=>99000,'size'=>['S','M'],'image'=>'/images/products/flannel-shirt.jpg','badge'=>'New'],
            ['id'=>14,'slug'=>'classic-khaki-shorts','name'=>'Classic Khaki Shorts','category'=>'classic-shirt','price'=>115000,'size'=>['M','L'],'image'=>'/images/products/flannel-1.jpg','badge'=>null],

            ['id'=>15,'slug'=>'blossom-pink-striped-shirt','name'=>'Blossom Pink Striped Shirt','category'=>'striped-shirt','price'=>129000,'size'=>['S','M'],'image'=>'/images/products/striped-shirt.jpg','badge'=>'Sale'],
            ['id'=>16,'slug'=>'classic-white-striped-shirt','name'=>'Classic White Striped Shirt','category'=>'striped-shirt','price'=>119000,'size'=>['M','L'],'image'=>'/images/products/striped-shirt.jpg','badge'=>null],
        ];
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingSelectedCategories(): void { $this->resetPage(); }
    public function updatingSelectedSizes(): void { $this->resetPage(); }
    public function updatingPriceMin(): void { $this->resetPage(); }
    public function updatingPriceMax(): void { $this->resetPage(); }

    public function toggleSize(string $size): void
    {
        if (in_array($size, $this->selectedSizes)) {
            $this->selectedSizes = array_values(array_diff($this->selectedSizes, [$size]));
        } else {
            $this->selectedSizes[] = $size;
        }
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategories = [];
        $this->selectedSizes = [];
        $this->priceMin = null;
        $this->priceMax = null;
        $this->resetPage();
    }

    public function getPageTitleProperty(): string
    {
        if ($this->slug && isset($this->categoryLabels[$this->slug])) {
            return $this->categoryLabels[$this->slug];
        }
        return __('category.all_products');
    }

    public function getCategoryCountsProperty(): array
    {
        $counts = [];
        foreach ($this->categoryLabels as $key => $label) {
            $counts[$key] = count(array_filter($this->allProducts, fn($p) => $p['category'] === $key));
        }
        return $counts;
    }

    public function getFilteredProductsProperty(): array
    {
        $products = $this->allProducts;

        if (!empty($this->selectedCategories)) {
            $products = array_filter($products, fn($p) => in_array($p['category'], $this->selectedCategories));
        }

        if (!empty($this->selectedSizes)) {
            $products = array_filter($products, fn($p) => count(array_intersect($p['size'], $this->selectedSizes)) > 0);
        }

        if ($this->priceMin !== null && $this->priceMin !== '') {
            $products = array_filter($products, fn($p) => $p['price'] >= (float) $this->priceMin);
        }

        if ($this->priceMax !== null && $this->priceMax !== '') {
            $products = array_filter($products, fn($p) => $p['price'] <= (float) $this->priceMax);
        }

        if (!empty($this->search)) {
            $term = strtolower($this->search);
            $products = array_filter($products, fn($p) => str_contains(strtolower($p['name']), $term));
        }

        return array_values($products);
    }

    public function getPaginatedProductsProperty(): array
    {
        $perPage = 6;
        $page = $this->getPage();
        $all = $this->filteredProducts;
        $offset = ($page - 1) * $perPage;

        return [
            'items' => array_slice($all, $offset, $perPage),
            'total' => count($all),
            'lastPage' => max(1, (int) ceil(count($all) / $perPage)),
            'currentPage' => $page,
        ];
    }
};
?>

<div class="wrap">

    <div class="breadcrumb">
        <div class="breadcrumb-inner">
            <a href="{{ route('home') }}">{{ __('category.home') }}</a> &nbsp;/&nbsp;
            <span class="current">{{ $this->pageTitle }}</span>
        </div>
    </div>

    <div class="page-title-block">
        <h1>{{ $this->pageTitle }}</h1>
        <div class="count">{{ $this->paginatedProducts['total'] }} {{ __('category.products_found') }}</div>
    </div>

    <div class="search-bar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="{{ __('category.search_placeholder') }}">
    </div>

    <div class="catalog-layout">

        {{-- SIDEBAR --}}
        <aside class="filters">
            <div class="filter-group">
                <h4>{{ __('category.product') }}</h4>
                @foreach($categoryLabels as $key => $label)
                    <label class="filter-option">
                        <input type="checkbox" wire:model.live="selectedCategories" value="{{ $key }}">
                        {{ $label }}
                        <span class="num">{{ $this->categoryCounts[$key] }}</span>
                    </label>
                @endforeach
            </div>

            <div class="filter-group">
                <h4>{{ __('category.size') }}</h4>
                <div class="size-chips">
                    @foreach(['S','M','L','XL'] as $size)
                        <div class="size-chip {{ in_array($size, $selectedSizes) ? 'active' : '' }}" wire:click="toggleSize('{{ $size }}')">
                            {{ $size }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-group">
                <h4>{{ __('category.price_range') }}</h4>
                <div class="price-range">
                    <input type="number" wire:model.live.debounce.500ms="priceMin" placeholder="{{ __('category.min') }}">
                    <span>—</span>
                    <input type="number" wire:model.live.debounce.500ms="priceMax" placeholder="{{ __('category.max') }}">
                </div>
            </div>

            <button class="clear-filters" wire:click="clearFilters">{{ __('category.clear_all_filters') }}</button>
        </aside>

        {{-- PRODUCT GRID --}}
        <div>
            <div class="category-grid">
                @forelse($this->paginatedProducts['items'] as $item)
                    <div class="product-card" wire:key="prod-{{ $item['id'] }}">
                        <div class="img-wrap">
                            @if($item['badge'])
                                <span class="product-badge">{{ $item['badge'] }}</span>
                            @endif
                            <button class="product-fav" title="{{ __('category.add_to_wishlist') }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.8 8.6c0-3.1-2.5-5.4-5.4-5.4-1.7 0-3.3.9-4.3 2.3-1-1.4-2.6-2.3-4.3-2.3-2.9 0-5.4 2.3-5.4 5.4 0 6 9.7 11.4 9.7 11.4s9.7-5.4 9.7-11.4z"/>
                                </svg>
                            </button>
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                        </div>
                        <div class="product-info">
                            <span class="cat-label">{{ $categoryLabels[$item['category']] }}</span>
                            <h4>{{ $item['name'] }}</h4>
                            <div class="price">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                            <a href="/product/{{ $item['slug'] }}" class="product-view-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                {{ __('category.view_product') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="no-results">{{ __('category.no_results') }}</div>
                @endforelse
            </div>

            @if($this->paginatedProducts['lastPage'] > 1)
                <div class="pagination-row">
                    <button class="page-btn" wire:click="previousPage" @if($this->paginatedProducts['currentPage'] <= 1) disabled @endif>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 6l-6 6 6 6"/></svg>
                    </button>

                    @for($i = 1; $i <= $this->paginatedProducts['lastPage']; $i++)
                        <button class="page-btn {{ $this->paginatedProducts['currentPage'] == $i ? 'active' : '' }}" wire:click="gotoPage({{ $i }})">
                            {{ $i }}
                        </button>
                    @endfor

                    <button class="page-btn" wire:click="nextPage" @if($this->paginatedProducts['currentPage'] >= $this->paginatedProducts['lastPage']) disabled @endif>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>
                    </button>
                </div>
            @endif
        </div>

    </div>
</div>