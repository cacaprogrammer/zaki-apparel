<div class="flex gap-2 items-center text-sm">
    <a href="{{ route('lang.switch', 'id') }}"
       class="px-2 py-1 rounded {{ app()->getLocale() === 'id' ? 'bg-gray-900 text-white' : 'text-gray-500' }}">
        ID
    </a>
    <span class="text-gray-300">|</span>
    <a href="{{ route('lang.switch', 'en') }}"
       class="px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-gray-900 text-white' : 'text-gray-500' }}">
        EN
    </a>
</div>