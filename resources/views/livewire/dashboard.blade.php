 <div>
     <header class="m-4 gap-4 grid justify-stretch sm:flex sm:justify-center flex-wrap" wire:loading.class="opacity-50">
         <x-dashboard.filters :$total :$month :$year :actions="$this->groupedAction()" :filters="$this->filtersAction()" />
         <x-dashboard.total :$total />
         <x-dashboard.summary :$summaries />
     </header>

     <div class="grid grid-cols-[repeat(auto-fit,_minmax(min(24rem,_100%),_1fr))] gap-4 p-2 sm:p-4 lg:p-8"
         wire:loading.class="opacity-50">
         @foreach ($categories as $category)
             <x-dashboard.category :category="$category" :categoryAddAction="$this->addToCategory" :noteAction="$this->editNoteAction" :key="'cat-' . $category->id" />
         @endforeach
     </div>

     <x-filament-actions::modals />
 </div>
