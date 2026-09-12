<?php

use Livewire\Component;

new class extends Component {
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }

    public function decrement(): void
    {
        if ($this->count > 0) {
            $this->count--;
        }
    }
}; ?>

<div class="flex flex-col items-center gap-4 p-8 border rounded-xl bg-white shadow-sm max-w-xs mx-auto">
    <h2 class="text-lg font-semibold text-gray-800">Livewire Test Counter</h2>

    <div class="text-4xl font-bold text-amber-600">
        {{ $count }}
    </div>

    <div class="flex gap-3">
        <button
            wire:click="decrement"
            class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition"
        >
            − Kurangi
        </button>

        <button
            wire:click="increment"
            class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition"
        >
            + Tambah
        </button>
    </div>
</div>