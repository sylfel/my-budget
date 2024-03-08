<section class="rounded-lg border border-black bg-white">
    @if (isset($header))
        <header class="p-4 flex border-b-2 gap-4">
            {{ $header }}
        </header>
    @endif
    <div class="px-8 py-4">
        {{ $slot }}
    </div>
    @if (isset($footer))
        <footer class="mt-6 flex justify-end px-8 py-4">
            {{ $footer }}
        </footer>
    @endif
</section>
