 <div>
     <header class="m-4 gap-4 grid justify-stretch sm:flex sm:justify-center flex-wrap" wire:loading.class="opacity-50">
         <x-section>
             <x-slot:header>
                 <h2 class="text-center grow">{{ __('Filters') }}</h2>
                 <span class="sm:hidden text-center font-bold {{ $total >= 0 ? 'text-green-600' : 'text-red-600' }}">
                     {{ Number::currency(($total ?? 0) / 100, 'EUR', 'fr') }}
                 </span>
             </x-slot:header>


             <form class="gap-4">
                 <div class="flex items-center ">
                     <select id="month" wire:model.live="month"
                         class="min-w-28 bg-gray-50 border border-r-0 border-gray-300 text-gray-900 text-sm rounded-lg rounded-r-none focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                         @foreach ($months as $monthname)
                             <option value='{{ $loop->index }}'>{{ Str::ucfirst($monthname) }}</option>
                         @endforeach
                     </select>
                     <select id="years" wire:model.live="year"
                         class="min-w-20 bg-gray-50 border border-l-0 border-gray-300 text-gray-900 text-sm rounded-lg rounded-l-none focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                         @foreach ($years as $year)
                             <option value='{{ $year }}'>{{ $year }}</option>
                         @endforeach
                     </select>
                     <div class="ml-2">
                         <x-filament-actions::group :actions="[$this->initMonthAction]" />
                     </div>
                 </div>
             </form>
         </x-section>

         <x-section class="hidden sm:block">
             <x-slot:header>
                 <h2 class="text-center grow">{{ __('Total') }}</h2>
             </x-slot:header>

             <div class="text-center font-bold {{ $total >= 0 ? 'text-green-600' : 'text-red-600' }}">
                 {{ Number::currency(($total ?? 0) / 100, 'EUR', 'fr') }}
             </div>

         </x-section>

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
     </header>
     <div class="grid grid-cols-[repeat(auto-fit,_minmax(min(30rem,_100%),_1fr))] gap-4 p-2 sm:p-4 lg:p-8">
         @foreach ($categories as $category)
             <x-section>
                 <x-slot:header>
                     @if ($category->icon)
                         <x-icon name="{{ $category->icon }}" class="w-6 h-6" />
                     @endif
                     <h3 class="grow">{{ $category->label }}</h3>
                     {{ ($this->addToCategoryAction)(['category' => $category->id]) }}

                     <span class="min-w-16 text-end {{ $category->credit ? 'text-green-600' : 'text-red-600' }}">
                         {{ Number::currency(($category->notes_sum_price ?? 0) / 100, 'EUR', 'fr') }}
                     </span>

                 </x-slot:header>

                 @foreach ($category->notes as $note)
                     <div class="flex justify-between">
                         <span class="grow">
                             {{ $note->poste ? $note->poste->label . ($note->label ? '. ' : '') : '' }}
                             {{ $note->label }}

                         </span>

                         <span class="mr-2"> {{ Number::currency($note->price, 'EUR', 'fr') }}</span>
                         {{ ($this->editNoteAction)(['note' => $note->id]) }}
                     </div>
                 @endforeach

             </x-section>
         @endforeach
     </div>

     <x-filament-actions::modals />
 </div>
