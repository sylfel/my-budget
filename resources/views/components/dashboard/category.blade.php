@props(['category', 'categoryAddAction', 'noteAction'])

<x-section>
    <x-slot:header>
        @if ($category->icon)
            <x-icon name="{{ $category->icon }}" class="w-6 h-6" />
        @endif
        <h3 class="grow">{{ $category->label }}</h3>
        @isset($categoryAddAction)
            {{ $categoryAddAction(['category' => $category->id]) }}
        @endisset

        <span class="min-w-16 text-end {{ $category->credit ? 'text-green-600' : 'text-red-600' }}">
            {{ Number::currency(($category->notes_sum_price ?? 0) / 100, 'EUR', 'fr') }}
        </span>

    </x-slot:header>
    {{-- each note within a poste --}}
    @foreach ($category->postes as $poste)
        @if ($poste->notes()->count() > 0)
            @if ($poste->notes->count() > 1)
                {{-- Header si more than one note --}}
                <x-dashboard.note :label="$poste->label" />
            @endif

            @foreach ($poste->notes as $note)
                @php
                    $action = isset($noteAction) ? $noteAction(['note' => $note->id]) : null;
                @endphp
                @if ($loop->count == 1)
                    <x-dashboard.note :label="$poste->label . ($note->label ? '. ' . $note->label : null)" :price="$note->price" :action="$action" />
                @else
                    <x-dashboard.note :label="$note->label" :price="$note->price" class="italic ml-2" :action="$action" />
                @endif
            @endforeach

            @if ($poste->notes->count() > 1)
                {{-- Summary if more than one note --}}
                <x-dashboard.note :price="$poste->notes_sum_price / 100" class="mb-2 font-bold" />
            @endif
        @endif
    @endforeach
    {{-- Each note without poste --}}
    @foreach ($category->notes as $note)
        @if ($note->poste_id == null)
            @php
                $action = isset($noteAction) ? $noteAction(['note' => $note->id]) : null;
            @endphp
            <x-dashboard.note :label="$note->label" :price="$note->price" :action="$action" />
        @endif
    @endforeach

</x-section>
