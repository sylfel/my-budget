 <div x-data="{ catMaxH: 'auto', catDisplay: 'block', catHeader: '2px' }">
     <header class="m-4 gap-4 grid justify-stretch sm:flex sm:justify-center flex-wrap" wire:loading.class="opacity-50">
         <x-dashboard.filters :$total :$month :$year :actions="$this->groupedAction()" :form="$this->form" />
         <x-dashboard.total :$total />
         <x-dashboard.summary :$summaries />
         <x-section>

             <x-slot:header>
                 <h2 class="text-center grow">{{ __('Display') }}</h2>
             </x-slot:header>

             <div class="flex md:flex-col gap-4">
                 <x-filament::button size="xs" outlined icon="heroicon-m-bars-4"
                     x-on:click="catMaxH = 'auto'; catDisplay = 'block', catHeader = '2px'">
                     Complet
                 </x-filament::button>
                 <x-filament::button size="xs" outlined icon="heroicon-m-bars-2"
                     x-on:click="catMaxH = '10em'; catDisplay = 'block', catHeader = '2px'">
                     Compact
                 </x-filament::button>
                 <x-filament::button size="xs" outlined icon="heroicon-m-minus"
                     x-on:click="catMaxH = '0'; catDisplay = 'none', catHeader = '0px'">
                     Minime
                 </x-filament::button>
             </div>
         </x-section>
     </header>

     <div class="grid grid-cols-[repeat(auto-fit,_minmax(min(24rem,_100%),_1fr))] gap-4 p-2 sm:p-4 lg:p-8"
         wire:loading.class="opacity-50"
         x-bind:style="'--section-display:' + catDisplay + '; --section-max-height:' + catMaxH + '; --section-bb:' + catHeader"
         @foreach ($categories as $category)
             <x-dashboard.category :$category :categoryAddAction="$this->addToCategory" :noteAction="$this->editNoteAction" :key="'cat-' . $category->id" /> @endforeach
         </div>

         <x-filament-actions::modals />
     </div>
