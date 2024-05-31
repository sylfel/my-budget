@props(['summaries'])

<x-section>
    @foreach ($summaries as $label => $montant)
        <div class="flex justify-between">
            <span class="grow">{{ $label }}</span>
            <span class="ml-8 {{ $montant >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ Number::currency((abs($montant) ?? 0) / 100, 'EUR', 'fr') }}
            </span>
        </div>
    @endforeach
</x-section>
