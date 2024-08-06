@props(['header', 'footer'])

<section {{ $attributes->merge(['class' => 'rounded-lg border border-black bg-white ']) }}>
    @if (isset($header))
        <header class="rounded-t-lg p-4 flex gap-4" style="border-bottom-width: var(--section-bb,2px)">
            {{ $header }}
        </header>
    @endif
    <div class="px-8 py-4 overflow-y-auto max-h-[--section-max-height]" style="display: var(--section-display, block)">
        {{ $slot }}
    </div>
    @if (isset($footer))
        <footer class="mt-6 flex justify-end px-8 py-4">
            {{ $footer }}
        </footer>
    @endif
</section>
