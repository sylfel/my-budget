<div class=" p-6 text-end z-10 bg-emerald-200 w-full shadow">
    @auth
        <a href="{{ url('/budget') }}"
            class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
            wire:navigate>{{ __('Budget') }}</a>
    @else
        <a href="{{ route('login') }}"
            class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
            wire:navigate>{{ __('Log in') }}</a>

        @if (Route::has('register'))
            <a href="{{ route('register') }}"
                class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                wire:navigate>{{ __('Register') }}</a>
        @endif
    @endauth
</div>
