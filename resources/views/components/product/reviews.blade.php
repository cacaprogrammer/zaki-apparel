<?php

use Livewire\Component;

new class extends Component {
    public string $activeTab = 'reviews';
    public string $sortBy = 'newest';

    public int $reviewsCount = 123;
    public float $ratingAvg = 4.8;
    public int $ratingTotal = 132;

    public array $ratingBreakdown = [
        5 => 108,
        4 => 17,
        3 => 5,
        2 => 1,
        1 => 1,
    ];

    public array $reviews = [];

    // Like state: menyimpan id review yang sudah di-like oleh user di sesi ini
    public array $likedReviews = [];

    // Reply state
    public ?string $replyingTo = null;
    public string $replyText = '';

    // New comment / review form
    public string $newCommentText = '';
    public int $newCommentRating = 5;

    public function mount(): void
    {
        // Dummy data — menggunakan ekstensi .png sesuai file kamu
        $this->reviews = [
            [
                'id' => 'r1',
                'name' => 'Jackson.graham',
                'rating' => 5,
                'date' => 'Yesterday',
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'likes' => 43,
                'avatar' => 'images/avatars/jackson.png',
                'replies' => [],
            ],
            [
                'id' => 'r2',
                'name' => 'Deana.cortis',
                'rating' => 5,
                'date' => '5 days ago',
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'likes' => 43,
                'avatar' => 'images/avatars/deana.png',
                'replies' => [],
            ],
            [
                'id' => 'r3',
                'name' => 'Akmalleca.young',
                'rating' => 4,
                'date' => '3 days ago',
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'likes' => 43,
                'avatar' => 'images/avatars/akmalleca.png',
                'replies' => [],
            ],
            [
                'id' => 'r4',
                'name' => 'Caca.young',
                'rating' => 5,
                'date' => '4 days ago',
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'likes' => 43,
                'avatar' => 'images/avatars/caca.png',
                'replies' => [],
            ],
        ];
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function updatedSortBy(): void
    {
        $this->reviews = match ($this->sortBy) {
            'oldest'  => array_reverse($this->reviews),
            'highest' => collect($this->reviews)->sortByDesc('rating')->values()->all(),
            'lowest'  => collect($this->reviews)->sortBy('rating')->values()->all(),
            default   => $this->reviews,
        };
    }

    public function getBarPercent(int $count): float
    {
        return $this->ratingTotal > 0 ? round(($count / $this->ratingTotal) * 100, 1) : 0;
    }

    public function toggleLike(string $id): void
    {
        foreach ($this->reviews as &$review) {
            if ($review['id'] === $id) {
                if (in_array($id, $this->likedReviews, true)) {
                    $this->likedReviews = array_values(array_diff($this->likedReviews, [$id]));
                    $review['likes'] = max(0, $review['likes'] - 1);
                } else {
                    $this->likedReviews[] = $id;
                    $review['likes']++;
                }
                break;
            }
        }
        unset($review);
    }

    public function openReply(string $id): void
    {
        // Klik lagi pada review yang sama -> tutup form reply
        $this->replyingTo = $this->replyingTo === $id ? null : $id;
        $this->replyText = '';
    }

    public function submitReply(string $id): void
    {
        $text = trim($this->replyText);
        if ($text === '') {
            return;
        }

        foreach ($this->reviews as &$review) {
            if ($review['id'] === $id) {
                $review['replies'][] = [
                    'name' => 'You',
                    'text' => $text,
                    'date' => 'Just now',
                ];
                break;
            }
        }
        unset($review);

        $this->replyText = '';
        $this->replyingTo = null;
    }

    public function submitComment(): void
    {
        $text = trim($this->newCommentText);
        if ($text === '') {
            return;
        }

        $newReview = [
            'id' => uniqid('r_'),
            'name' => 'You',
            'rating' => $this->newCommentRating,
            'date' => 'Just now',
            'text' => $text,
            'likes' => 0,
            'avatar' => '',
            'replies' => [],
        ];

        array_unshift($this->reviews, $newReview);
        $this->reviewsCount++;
        $this->newCommentText = '';
        $this->newCommentRating = 5;
    }
};
?>

<div class="reviews-wrap">
    <div class="reviews-tabs">
        <button class="tab-btn {{ $activeTab === 'description' ? 'active' : '' }}" wire:click="setTab('description')">
            {{ __('product.tab_description') }}
        </button>
        <button class="tab-btn {{ $activeTab === 'reviews' ? 'active' : '' }}" wire:click="setTab('reviews')">
            {{ __('product.tab_reviews') }} ({{ $reviewsCount }})
        </button>
        <button class="tab-btn {{ $activeTab === 'discussion' ? 'active' : '' }}" wire:click="setTab('discussion')">
            {{ __('product.tab_discussion') }}
        </button>
    </div>

    @if($activeTab === 'reviews')
        <div class="reviews-body">
            <div class="reviews-list-col">

                <div class="reviews-sort-row">
                    <select wire:model.live="sortBy" class="sort-select">
                        <option value="newest">{{ __('product.sort_newest') }}</option>
                        <option value="oldest">{{ __('product.sort_oldest') }}</option>
                        <option value="highest">{{ __('product.sort_highest') }}</option>
                        <option value="lowest">{{ __('product.sort_lowest') }}</option>
                    </select>
                </div>

                {{-- FORM TAMBAH KOMENTAR / REVIEW BARU --}}
                <div class="write-review-box">
                    <div class="write-review-head">
                        <span class="label">{{ __('product.write_review') }}</span>
                        <div class="write-review-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star-select {{ $i <= $newCommentRating ? 'active' : '' }}" wire:click="$set('newCommentRating', {{ $i }})">★</span>
                            @endfor
                        </div>
                    </div>
                    <textarea
                        wire:model="newCommentText"
                        class="write-review-input"
                        rows="3"
                        placeholder="{{ __('product.write_review_placeholder') }}"
                    ></textarea>
                    <button class="btn-submit-review" wire:click="submitComment">{{ __('product.submit_review') }}</button>
                </div>

                @foreach($reviews as $review)
                    <div class="review-card">
                        <img 
                            class="review-avatar" 
                            src="{{ $review['avatar'] ? asset($review['avatar']) : 'https://ui-avatars.com/api/?name=' . urlencode($review['name']) . '&background=E5E5E5&color=1B1B22' }}" 
                            alt="{{ $review['name'] }}"
                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($review['name']) }}&background=E5E5E5&color=1B1B22';"
                        >
                        <div class="review-content">
                            <div class="review-top-row">
                                <span class="review-name">{{ $review['name'] }}</span>
                                <span class="review-date">{{ $review['date'] }}</span>
                            </div>
                            <div class="review-stars">{{ str_repeat('★', $review['rating']) }}{{ str_repeat('☆', 5 - $review['rating']) }}</div>
                            <p class="review-text">{{ $review['text'] }}</p>
                            <div class="review-actions">
                                <button
                                    class="review-like {{ in_array($review['id'], $likedReviews, true) ? 'active' : '' }}"
                                    wire:click="toggleLike('{{ $review['id'] }}')"
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 10v12"/>
                                        <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z"/>
                                    </svg>
                                    {{ $review['likes'] }}
                                </button>
                                <button class="review-reply" wire:click="openReply('{{ $review['id'] }}')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
                                    </svg>
                                    {{ __('product.reply') }}
                                </button>
                            </div>

                            {{-- FORM BALAS REVIEW --}}
                            @if($replyingTo === $review['id'])
                                <div class="reply-form">
                                    <textarea
                                        wire:model="replyText"
                                        rows="2"
                                        placeholder="{{ __('product.reply_placeholder') }}"
                                    ></textarea>
                                    <div class="reply-form-actions">
                                        <button class="btn-cancel-reply" wire:click="openReply('{{ $review['id'] }}')">
                                            {{ __('product.cancel') }}
                                        </button>
                                        <button class="btn-submit-reply" wire:click="submitReply('{{ $review['id'] }}')">
                                            {{ __('product.send') }}
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- DAFTAR BALASAN --}}
                            @if(!empty($review['replies']))
                                <div class="review-replies">
                                    @foreach($review['replies'] as $reply)
                                        <div class="reply-item">
                                            <div class="reply-top-row">
                                                <span class="reply-name">{{ $reply['name'] }}</span>
                                                <span class="reply-date">{{ $reply['date'] }}</span>
                                            </div>
                                            <p class="reply-text">{{ $reply['text'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($activeTab === 'description')
        <div class="tab-plain-panel">
            <p>{{ __('product.description_placeholder') }}</p>
        </div>
    @else
        <div class="tab-plain-panel">
            <p>{{ __('product.discussion_placeholder') }}</p>
        </div>
    @endif
</div>