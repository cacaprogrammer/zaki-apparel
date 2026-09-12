<x-layouts.user :title="__('cart.page_title') . ' — Zaki Apparel'">
    @vite(['resources/css/landing.css', 'resources/css/cart.css'])

    <livewire:cart />
</x-layouts.user>