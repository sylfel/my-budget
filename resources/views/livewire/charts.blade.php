<div class="p-4">
    <form class="flex gap-4 items-center justify-center">
        <span>Nombre de mois : </span>
        <div class="min-w-16">
            <select id="nbMonths" wire:model.live="nbMonths"
                class="min-w-28 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="2">3 Mois</option>
                <option value="5">6 Mois</option>
                <option value="11">12 Mois</option>
            </select>
        </div>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 h-[min(40rem,80svh)]">
        @foreach ($charts as ['title' => $title, 'labels' => $labels, 'datasets' => $datasets, 'name' => $name])
            <livewire:chart :key="$name" :title="$title" :labels="$labels" :datasets="$datasets" :name="$name" />
        @endforeach

        @foreach ($tables as $table)
            <div class="col-span-2 grid">
                <table class="border-collapse border border-slate-400 text-right">
                    <caption class="caption-top">
                        {{ $table['name'] }}
                    </caption>
                    <thead>
                        <th class="bg-slate-400"></th>
                        @foreach ($table['labels'] as $label)
                            <th scope="col" class="text-lg py-2 bg-slate-400">{{ $label }}</th>
                        @endforeach
                    </thead>
                    <tbody>
                        @foreach ($table['datasets'] as $key => $dataset)
                            @php
                                $lastValue = null;
                            @endphp
                            <tr class="odd:bg-white even:bg-slate-200">
                                <th scope="row" class="pt-2 pb-4 text-lg">
                                    {{ $key }}</th>
                                @foreach ($dataset as $data)
                                    @php
                                        $diff = $lastValue ? $data - $lastValue : null;
                                    @endphp
                                    <td class="align-top">
                                        <div>{{ number_format($data, 2, ',', ' ') }}</div>
                                        @if ($diff)
                                            <div
                                                class="text-sm italic {{ $diff > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                {{ number_format($diff, 2, ',', ' ') }}</div>
                                        @endif
                                    </td>
                                    @php
                                        $lastValue = $data;
                                    @endphp
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</div>
