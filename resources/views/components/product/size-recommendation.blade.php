<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    public bool $show = false;
    public string $step = 'form'; // 'form' | 'result'

    public ?float $height = null;
    public ?float $weight = null;
    public ?float $chest = null;

    public string $resultSize = '';
    public string $resultText = '';
    public bool $hasError = false;

    #[On('open-size-modal')]
    public function open(): void
    {
        $this->show = true;
        $this->step = 'form';
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function getMySize(): void
    {
        if (!$this->height || !$this->weight || !$this->chest || $this->height <= 0 || $this->weight <= 0 || $this->chest <= 0) {
            $this->hasError = true;
            return;
        }

        $this->hasError = false;

        $sizes = ['S', 'M', 'L', 'XL'];
        $index = 0;

        if ($this->chest < 56) {
            $index = 0;
        } elseif ($this->chest < 63) {
            $index = 1;
        } elseif ($this->chest < 71) {
            $index = 2;
        } else {
            $index = 3;
        }

        if ($this->height >= 130 && $index < 3) {
            $index++;
        } elseif ($this->height < 95 && $index > 0) {
            $index--;
        }

        $this->resultSize = $sizes[$index];

        $fitNotes = [
            'S' => 'a snug, true-to-size fit — great for a neat, tidy look.',
            'M' => 'a balanced, comfortable fit with a little room to move.',
            'L' => 'a relaxed fit with extra room to grow into over the next few months.',
            'XL' => 'an extra-roomy fit, ideal if your child prefers looser, breathable clothing.',
        ];

        $this->resultText = "Based on a height of {$this->height} cm, weight of {$this->weight} kg, and chest of {$this->chest} cm — " . $fitNotes[$this->resultSize];

        $this->step = 'result';
    }

    public function tryAgain(): void
    {
        $this->step = 'form';
    }

    public function useThisSize(): void
    {
        $this->dispatch('size-selected', size: $this->resultSize);
        $this->show = false;
    }
}; ?>

<div>
    @if($show)
        <div class="modal-overlay" wire:click.self="close">
            <div class="size-modal">
                <button class="modal-close" wire:click="close">&times;</button>
                <div class="size-modal-scroll">
                @if($step === 'form')
                    <div class="modal-eyebrow">{{ __('product.size_recommendation') }}</div>
                    <h2>{{ __('product.find_perfect_size') }}</h2>
                    <p class="sub">{{ __('product.size_modal_desc') }}</p>

                    <div class="field-group">
                        <label>{{ __('product.height') }}</label>
                        <div class="field-input-wrap">
                            <input type="number" wire:model="height" placeholder="e.g. 120">
                            <span class="unit">cm</span>
                        </div>
                    </div>

                    <div class="field-group">
                        <label>{{ __('product.weight') }}</label>
                        <div class="field-input-wrap">
                            <input type="number" wire:model="weight" placeholder="e.g. 24">
                            <span class="unit">kg</span>
                        </div>
                    </div>

                    <div class="field-group">
                        <label>{{ __('product.chest') }}</label>
                        <div class="field-input-wrap">
                            <input type="number" wire:model="chest" placeholder="e.g. 62">
                            <span class="unit">cm</span>
                        </div>
                    </div>

                    @if($hasError)
                        <div class="size-error">{{ __('product.size_error') }}</div>
                    @endif

                    <button class="btn-get-size" wire:click="getMySize">{{ __('product.get_my_size') }}</button>

                    <div class="modal-note">
                        <span class="note-icon">!</span>
                        <span>{{ __('product.size_note') }}</span>
                    </div>
                @else
                    <div class="size-result">
                        <div class="modal-eyebrow">{{ __('product.size_recommendation') }}</div>
                        <div class="circle">{{ $resultSize }}</div>
                        <h3>{{ __('product.we_recommend', ['size' => $resultSize]) }}</h3>
                        <p>{{ $resultText }}</p>
                        <div class="size-result-actions">
                            <button class="btn-try-again" wire:click="tryAgain">{{ __('product.try_again') }}</button>
                            <button class="btn-use-size" wire:click="useThisSize">{{ __('product.use_this_size') }}</button>
                        </div>
                    </div>
                @endif
                </div>
            </div>
        </div>
    @endif
</div>