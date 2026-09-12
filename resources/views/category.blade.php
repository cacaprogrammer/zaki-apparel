<x-layouts.user :title="__('category.page_title') . ' — Zaki Apparel'">
    @vite(['resources/css/landing.css', 'resources/css/category.css'])

    <livewire:category :slug="$slug" />
</x-layouts.user>