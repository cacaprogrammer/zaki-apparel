<x-layouts.app title="Test Livewire — Zaki Apparel">
    <div class="min-h-screen flex flex-col items-center justify-center gap-6">

        <x-language-switcher />

        <div class="text-center">
            <h1 class="text-xl font-bold">{{ __('app.greeting') }}</h1>
            <p class="text-gray-500">{{ __('app.tagline') }}</p>
            <p class="text-sm text-gray-400 mt-1">{{ __('app.current_language') }}</p>
        </div>

        <livewire:counter />
    </div>
</x-layouts.app>