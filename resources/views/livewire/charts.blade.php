<div class="p-4">
    <form class="flex gap-4 items-center justify-center">
        <span>Nombre de mois : </span>
        <div class="min-w-16">
            <select id="nbMonths" wire:model.live="nbMonths"
                class="min-w-28 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="3">3 Mois</option>
                <option value="6">6 Mois</option>
                <option value="12">12 Mois</option>
            </select>
        </div>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 h-[min(40rem,80svh)]">
        @foreach ($charts as ['title' => $title, 'labels' => $labels, 'datasets' => $datasets, 'name' => $name])
            <livewire:chart :key="$name" :title="$title" :labels="$labels" :datasets="$datasets" :name="$name" />
        @endforeach
    </div>
</div>
