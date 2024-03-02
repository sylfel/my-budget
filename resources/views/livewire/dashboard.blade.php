 <div>
     <header class="m-4 flex justify-center" wire:loading.class="opacity-50">
         <x-section>
             <x-slot:header>
                 <h2 class="text-center grow">Filtres</h2>
             </x-slot:header>


             <form class="max-w-sm mx-auto flex gap-4">
                 <div class="flex items-center min-w-48">
                     <select id="month" wire:model.live="month"
                         class="bg-gray-50 border border-r-0 border-gray-300 text-gray-900 text-sm rounded-lg rounded-r-none focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                         @foreach ($months as $monthname)
                             <option value='{{ $loop->index }}'>{{ Str::ucfirst($monthname) }}</option>
                         @endforeach
                     </select>
                     <select id="years" wire:model.live="year"
                         class="bg-gray-50 border border-l-0 border-gray-300 text-gray-900 text-sm rounded-lg rounded-l-none focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                         @foreach ($years as $year)
                             <option value='{{ $year }}'>{{ $year }}</option>
                         @endforeach
                     </select>
                 </div>
             </form>
         </x-section>
     </header>
     <div class="grid grid-cols-5 gap-4 p-8">
         @foreach ($categories as $category)
             <x-section>
                 <x-slot:header>
                     <h3 class="grow">{{ $category->label }}</h3>
                     <x-secondary-button class="mx-4" x-data=""
                         x-on:click="$dispatch('openModal', 
                         { component: 'edit-note', 
                         arguments: { category: {{ $category->id }}, year: {{ $year }}, month: {{ $month }} }})">{{ __('Add note') }}</x-secondary-button>

                     <span class="min-w-16 text-end {{ $category->credit ? 'text-green-600' : 'text-red-600' }}">
                         {{ Number::currency(($category->notes_sum_price ?? 0) / 100, 'EUR', 'fr') }}
                     </span>

                 </x-slot:header>

                 @foreach ($category->notes as $note)
                     <div class="flex justify-between">
                         <span>
                             {{ $note->poste ? $note->poste->label . ($note->label ? '. ' : '') : '' }}
                             {{ $note->label }}</span>
                         <span> {{ Number::currency($note->price, 'EUR', 'fr') }}</span>
                     </div>
                 @endforeach

             </x-section>
         @endforeach
     </div>
 </div>
