<div>
    <x-section>
        <x-slot:header>
            <h2>Add Note to <span class="font-bold">{{ $category->label }}</span></h2>
        </x-slot:header>

        <form id="edit-note" class="max-w-sm mx-auto grid grid-cols-[5rem,_1fr] gap-4 items-baseline" wire:submit="save">

            @error('price')
                <span class="error col-span-2">{{ $message }}</span>
            @enderror
            <label for="currency-input" class="mb-2 text-sm font-medium text-gray-900 dark:text-white">Montant</label>
            <input type="number" id="currency-input" step="0.01" wire:model="price"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Montant" required />


            @if ($category->postes->count() > 0)
                @error('poste')
                    <span class="error col-span-2">{{ $message }}</span>
                @enderror
                <label for="poste" class="">Poste</label>
                <select id="poste" wire:model="poste"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option>Select a value</option>
                    @foreach ($category->postes as $poste)
                        <option value='{{ $poste->id }}'>{{ $poste->label }}</option>
                    @endforeach
                </select>
            @endif

            @error('label')
                <span class="error col-span-2">{{ $message }}</span>
            @enderror
            <label for="label" class="">Label</label>
            <input type="text" id="label" wire:model="label"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Description" />
        </form>

        <x-slot:footer>
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ms-3" form="edit-note">
                {{ __('Add note') }}
            </x-primary-button>
        </x-slot:footer>
    </x-section>
</div>
